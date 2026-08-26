<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $nameAr = trim((string) ($row['name_ar'] ?? ''));
            $productSku = trim((string) ($row['product_sku'] ?? ''));

            // نتجاهل الصف إذا لم يوجد اسم أو كود منتج
            if ($nameAr === '' || $productSku === '') {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */

            $category = null;
            $categoryAr = trim((string) ($row['category_ar'] ?? ''));
            $categoryEn = trim((string) ($row['category_en'] ?? ''));

            if ($categoryAr !== '') {

                $category = Category::where('name_ar', $categoryAr)->first();

                if (!$category) {
                    $category = new Category();
                    $category->name_ar = $categoryAr;
                    $category->name_en = $categoryEn !== '' ? $categoryEn : null;

                    $categorySlug = Str::slug(
                        $categoryEn !== '' ? $categoryEn : $categoryAr
                    );

                    $category->slug = $categorySlug !== ''
                        ? $categorySlug
                        : 'category-' . Str::lower(Str::random(8));

                    $category->save();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */

            $product = Product::where('sku', $productSku)->first();

            if (!$product) {
                $product = new Product();
                $product->sku = $productSku;
            }

            $product->category_id = $category?->id;

            $product->name_ar = $nameAr;
            $product->name_en = $this->nullable($row['name_en'] ?? null);

            $slugSource = $this->nullable($row['name_en'] ?? null) ?: $nameAr;
            $slug = Str::slug($slugSource);

            $product->slug = ($slug !== '' ? $slug : 'product')
                . '-' . Str::slug($productSku);

            $product->description_ar =
                $this->nullable($row['description_ar'] ?? null);

            $product->description_en =
                $this->nullable($row['description_en'] ?? null);

            $product->price =
                (float) ($row['price'] ?? 0);

            $product->compare_price =
                $this->numberOrNull($row['compare_price'] ?? null);

            $product->cost_price =
                $this->numberOrNull($row['cost_price'] ?? null);

            $product->is_active =
                $this->toBool($row['is_active'] ?? true);

            $product->is_featured =
                $this->toBool($row['is_featured'] ?? false);

            $product->is_new =
                $this->toBool($row['is_new'] ?? false);

            $product->is_best_seller =
                $this->toBool($row['is_best_seller'] ?? false);

            $product->save();

            /*
            |--------------------------------------------------------------------------
            | Variant: Color + Size + Stock
            |--------------------------------------------------------------------------
            */

            $colorAr = $this->nullable($row['color_ar'] ?? null);
            $colorEn = $this->nullable($row['color_en'] ?? null);
            $colorCode = $this->nullable($row['color_code'] ?? null);

            // يقبل 36، 37، 38، 38.5 وكذلك S / M / L
            $size = $this->nullable($row['size'] ?? null);

            $variantSku =
                $this->nullable($row['variant_sku'] ?? null);

            if (!$variantSku) {
                $variantParts = array_filter([
                    $productSku,
                    $colorEn ?: $colorAr,
                    $size,
                ]);

                $variantSku = Str::upper(
                    Str::slug(implode('-', $variantParts), '-')
                );
            }

            $variant = ProductVariant::where('sku', $variantSku)->first();

            if (!$variant) {
                $variant = new ProductVariant();
                $variant->sku = $variantSku;
            }

            $variant->product_id = $product->id;
            $variant->color_name_ar = $colorAr;
            $variant->color_name_en = $colorEn;
            $variant->color_code = $colorCode;
            $variant->size = $size;

            $variant->stock_quantity =
                (int) ($row['stock_quantity'] ?? 0);

            $variant->price =
                $this->numberOrNull($row['variant_price'] ?? null);

            $variant->image =
                $this->nullable($row['variant_image'] ?? null);

            $variant->is_active =
                $this->toBool($row['variant_active'] ?? true);

            $variant->save();

            /*
            |--------------------------------------------------------------------------
            | Product Images
            |--------------------------------------------------------------------------
            |
            | image_1 / image_2 / image_3
            | تقبل اسم الصورة أو رابطها.
            |
            */

            foreach (['image_1', 'image_2', 'image_3'] as $index => $column) {

                $image = $this->nullable($row[$column] ?? null);

                if (!$image) {
                    continue;
                }

                $exists = ProductImage::where('product_id', $product->id)
                    ->whereNull('product_variant_id')
                    ->where('image', $image)
                    ->exists();

                if (!$exists) {
                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->product_variant_id = null;
                    $productImage->image = $image;
                    $productImage->is_primary = ($index === 0);
                    $productImage->sort_order = $index;
                    $productImage->save();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Variant Image
            |--------------------------------------------------------------------------
            */

            $variantImage =
                $this->nullable($row['variant_image'] ?? null);

            if ($variantImage) {

                $exists = ProductImage::where('product_id', $product->id)
                    ->where('product_variant_id', $variant->id)
                    ->where('image', $variantImage)
                    ->exists();

                if (!$exists) {
                    $image = new ProductImage();
                    $image->product_id = $product->id;
                    $image->product_variant_id = $variant->id;
                    $image->image = $variantImage;
                    $image->is_primary = false;
                    $image->sort_order = 0;
                    $image->save();
                }
            }
        }
    }

    private function nullable($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function numberOrNull($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    private function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim((string) $value));

        return in_array($value, [
            '1',
            'true',
            'yes',
            'y',
            'نعم',
        ], true);
    }
}