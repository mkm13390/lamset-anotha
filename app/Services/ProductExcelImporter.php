<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExcelImporter
{
    public function import(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();

        $headers = [];

        $firstRow = $sheet->rangeToArray(
            "A1:{$highestColumn}1"
        )[0];

        foreach ($firstRow as $index => $header) {
            $headerText = trim((string) $header);

            // قراءة الجزء الإنجليزي قبل علامة |
            $englishHeader = trim(
                explode('|', $headerText, 2)[0]
            );

            $headers[$index + 1] = Str::snake($englishHeader);
        }

        $drawingsByRow = $this->getDrawingsByRow($sheet);

        $created = 0;
        $updated = 0;
        $variants = 0;
        $images = 0;

        DB::transaction(function () use (
            $sheet,
            $highestRow,
            $highestColumn,
            $headers,
            $drawingsByRow,
            &$created,
            &$updated,
            &$variants,
            &$images
        ) {
            for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
                $rowValues = $sheet->rangeToArray(
                    "A{$rowNumber}:{$highestColumn}{$rowNumber}",
                    null,
                    true,
                    true,
                    false
                )[0];

                $row = [];

                foreach ($rowValues as $index => $value) {
                    $columnNumber = $index + 1;

                    if (isset($headers[$columnNumber])) {
                        $row[$headers[$columnNumber]] = $value;
                    }
                }

                $sku = trim(
                    (string) ($row['product_sku'] ?? '')
                );

                if ($sku === '') {
                    continue;
                }

                [$nameEn, $nameAr] = $this->splitBilingual(
                    $row['product_name'] ?? ''
                );

                [$categoryEn, $categoryAr] = $this->splitBilingual(
                    $row['category'] ?? ''
                );

                $category = $this->getOrCreateCategory(
                    $categoryAr,
                    $categoryEn
                );

                $product = Product::where('sku', $sku)->first();

                if ($product) {
                    $updated++;
                } else {
                    $product = new Product();
                    $product->sku = $sku;
                    $created++;
                }

                $product->category_id = $category?->id;
                $product->name_ar = $nameAr ?: $nameEn ?: $sku;
                $product->name_en = $this->nullable($nameEn);

                $slugBase = $nameEn ?: $nameAr ?: 'product';

                $slug = Str::slug($slugBase);

                $product->slug = ($slug !== '' ? $slug : 'product')
                    . '-' . Str::slug($sku);

                $product->description_ar = $this->nullable(
                    $row['description_ar'] ?? null
                );

                $product->description_en = $this->nullable(
                    $row['description_en'] ?? null
                );

                $product->price = $this->numberOrZero(
                    $row['price'] ?? 0
                );

                $product->compare_price = $this->numberOrNull(
                    $row['compare_price'] ?? null
                );

                $product->cost_price = $this->numberOrNull(
                    $row['cost_price'] ?? null
                );

                $product->is_active = $this->toBool(
                    $row['is_active'] ?? true
                );

                $product->is_featured = $this->toBool(
                    $row['is_featured'] ?? false
                );

                $product->is_new = $this->toBool(
                    $row['is_new'] ?? false
                );

                $product->is_best_seller = $this->toBool(
                    $row['is_best_seller'] ?? false
                );

                $product->save();

                $variant = $this->saveVariant($product, $row);

                if ($variant) {
                    $variants++;
                }

                if (isset($drawingsByRow[$rowNumber])) {
                    foreach (
                        $drawingsByRow[$rowNumber] as $index => $drawing
                    ) {
                        $storedPath = $this->storeDrawing(
                            $drawing,
                            $product->sku,
                            $rowNumber,
                            $index
                        );

                        if (!$storedPath) {
                            continue;
                        }

                        $productImage = ProductImage::where(
                            'product_id',
                            $product->id
                        )
                            ->where('image', $storedPath)
                            ->first();

                        if (!$productImage) {
                            $productImage = new ProductImage();
                            $productImage->product_id = $product->id;
                            $productImage->product_variant_id =
                                $variant?->id;
                            $productImage->image = $storedPath;
                            $productImage->is_primary =
                                !$product->images()->exists();
                            $productImage->sort_order = $index;
                            $productImage->save();

                            $images++;
                        }
                    }
                }
            }
        });

        return [
            'created' => $created,
            'updated' => $updated,
            'variants' => $variants,
            'images' => $images,
        ];
    }

    private function getOrCreateCategory(
        ?string $nameAr,
        ?string $nameEn
    ): ?Category {
        if (!$nameAr && !$nameEn) {
            return null;
        }

        $category = Category::query()
            ->when(
                $nameAr,
                fn ($query) => $query->where('name_ar', $nameAr)
            )
            ->first();

        if ($category) {
            return $category;
        }

        $category = new Category();
        $category->name_ar = $nameAr ?: $nameEn;
        $category->name_en = $nameEn;

        $slugBase = $nameEn ?: $nameAr ?: 'category';
        $slug = Str::slug($slugBase);

        if ($slug === '') {
            $slug = 'category-' . Str::lower(Str::random(8));
        }

        $originalSlug = $slug;
        $counter = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $category->slug = $slug;
        $category->save();

        return $category;
    }

    private function saveVariant(
        Product $product,
        array $row
    ): ?ProductVariant {
        [$colorEn, $colorAr] = $this->splitBilingual(
            $row['color'] ?? ''
        );

        $colorCode = $this->nullable(
            $row['color_code'] ?? null
        );

        $size = $this->nullable(
            $row['size'] ?? null
        );

        if (!$colorAr && !$colorEn && !$colorCode && !$size) {
            return null;
        }

        $variantSku = $this->nullable(
            $row['variant_sku'] ?? null
        );

        if (!$variantSku) {
            $parts = array_filter([
                $product->sku,
                $colorEn ?: $colorAr,
                $size,
            ]);

            $variantSku = Str::upper(
                Str::slug(implode('-', $parts), '-')
            );
        }

        $variant = ProductVariant::where(
            'sku',
            $variantSku
        )->first();

        if (!$variant) {
            $variant = new ProductVariant();
            $variant->sku = $variantSku;
        }

        $variant->product_id = $product->id;
        $variant->color_name_ar = $colorAr;
        $variant->color_name_en = $colorEn;
        $variant->color_code = $colorCode;
        $variant->size = $size;

        $variant->stock_quantity = max(
            0,
            (int) ($row['stock_quantity'] ?? 0)
        );

        $variant->price = $this->numberOrNull(
            $row['variant_price'] ?? null
        );

        $variant->is_active = $this->toBool(
            $row['variant_is_active'] ?? true
        );

        $variant->save();

        return $variant;
    }

    private function splitBilingual(mixed $value): array
    {
        $value = trim((string) $value);

        if ($value === '') {
            return [null, null];
        }

        $parts = explode('/', $value, 2);

        $english = $this->nullable($parts[0] ?? null);
        $arabic = $this->nullable($parts[1] ?? null);

        if (!$arabic) {
            $arabic = $english;
        }

        return [$english, $arabic];
    }

    private function getDrawingsByRow(
        Worksheet $sheet
    ): array {
        $drawingsByRow = [];

        foreach ($sheet->getDrawingCollection() as $drawing) {
            $coordinates = $drawing->getCoordinates();

            if (!$coordinates) {
                continue;
            }

            [, $rowNumber] = Coordinate::coordinateFromString(
                $coordinates
            );

            $drawingsByRow[(int) $rowNumber][] = $drawing;
        }

        return $drawingsByRow;
    }

    private function storeDrawing(
        mixed $drawing,
        string $sku,
        int $rowNumber,
        int $index
    ): ?string {
        $contents = null;
        $extension = 'png';

        if ($drawing instanceof MemoryDrawing) {
            ob_start();

            $renderingFunction = $drawing->getRenderingFunction();

            if (is_callable($renderingFunction)) {
                $renderingFunction(
                    $drawing->getImageResource()
                );
            }

            $contents = ob_get_clean();

            $extension = match ($drawing->getMimeType()) {
                MemoryDrawing::MIMETYPE_JPEG => 'jpg',
                MemoryDrawing::MIMETYPE_GIF => 'gif',
                default => 'png',
            };
        } elseif ($drawing instanceof Drawing) {
            $path = $drawing->getPath();

            if (!$path) {
                return null;
            }

            $contents = @file_get_contents($path);

            if (method_exists($drawing, 'getExtension')) {
                $extension = $drawing->getExtension() ?: 'png';
            } else {
                $extension = pathinfo(
                    parse_url($path, PHP_URL_PATH) ?: $path,
                    PATHINFO_EXTENSION
                ) ?: 'png';
            }
        }

        if (!$contents) {
            return null;
        }

        $safeSku = Str::slug($sku, '-');

        if ($safeSku === '') {
            $safeSku = 'product';
        }

        $storedPath =
            'products/imported/'
            . $safeSku
            . '/'
            . $safeSku
            . '-'
            . $rowNumber
            . '-'
            . $index
            . '.'
            . strtolower($extension);

        Storage::disk('public')->put(
            $storedPath,
            $contents
        );

        return $storedPath;
    }

    private function nullable(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function numberOrNull(mixed $value): ?float
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = str_replace(',', '', (string) $value);

        return is_numeric($value) ? (float) $value : null;
    }

    private function numberOrZero(mixed $value): float
    {
        return $this->numberOrNull($value) ?? 0.0;
    }

    private function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        $value = Str::lower(
            trim((string) $value)
        );

        return in_array(
            $value,
            ['1', 'true', 'yes', 'on', 'نعم', 'نشط'],
            true
        );
    }
}