<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductExcelImporter;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductImportController extends Controller
{
    public function create()
    {
        return view('admin.products.import');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('المنتجات');
        $sheet->setRightToLeft(true);

        $headers = [
            'product_sku | رمز المنتج',
            'product_name | اسم المنتج English/العربية',
            'category | القسم English/العربية',
            'description_ar | الوصف بالعربية',
            'description_en | الوصف بالإنجليزية',
            'price | سعر البيع',
            'compare_price | السعر قبل الخصم',
            'cost_price | سعر التكلفة',
            'is_active | حالة المنتج',
            'is_featured | منتج مميز',
            'is_new | وصل حديثاً',
            'is_best_seller | الأكثر مبيعاً',
            'variant_sku | رمز الخيار',
            'color | اللون English/العربية',
            'color_code | كود اللون',
            'size | المقاس',
            'stock_quantity | الكمية',
            'variant_price | سعر الخيار',
            'variant_is_active | حالة الخيار',
            'image | صورة المنتج',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $sheet->getStyle('A1:T1')->getFont()
            ->setBold(true)
            ->setSize(12);

        $sheet->getStyle('A1:T1')->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center')
            ->setWrapText(true);

        $sheet->getStyle('A1:T1')->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setARGB('FFD9EAF7');

        $sheet->getRowDimension(1)->setRowHeight(40);
        $sheet->freezePane('A2');

        foreach (range('A', 'T') as $column) {
            $sheet->getColumnDimension($column)->setWidth(24);
        }

        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('T')->setWidth(25);

        /*
        |--------------------------------------------------------------------------
        | دليل الاستخدام
        |--------------------------------------------------------------------------
        */

        $guide = $spreadsheet->createSheet();
        $guide->setTitle('دليل الاستخدام');
        $guide->setRightToLeft(true);

        $guideData = [
            ['العمود', 'طريقة التعبئة'],
            ['product_sku', 'رمز فريد للمنتج مثل BAG-001'],
            ['product_name', 'الإنجليزية ثم / ثم العربية: Classic Bag/حقيبة كلاسيكية'],
            ['category', 'الإنجليزية ثم / ثم العربية: Bags/حقائب'],
            ['description_ar', 'وصف المنتج بالعربية'],
            ['description_en', 'وصف المنتج بالإنجليزية'],
            ['price', 'سعر البيع الأساسي'],
            ['compare_price', 'السعر قبل الخصم، ويمكن تركه فارغًا'],
            ['cost_price', 'سعر التكلفة، ويمكن تركه فارغًا'],
            ['is_active', 'اكتب 1 للنشط أو 0 لغير النشط'],
            ['is_featured', 'اكتب 1 للمنتج المميز أو 0'],
            ['is_new', 'اكتب 1 لوصل حديثًا أو 0'],
            ['is_best_seller', 'اكتب 1 للأكثر مبيعًا أو 0'],
            ['variant_sku', 'رمز اللون والمقاس مثل BAG-001-BLK-M'],
            ['color', 'الإنجليزية ثم / ثم العربية: Black/أسود'],
            ['color_code', 'كود اللون مثل #000000'],
            ['size', 'المقاس مثل 38 أو M'],
            ['stock_quantity', 'الكمية المتوفرة'],
            ['variant_price', 'سعر خاص بالخيار، ويمكن تركه فارغًا'],
            ['variant_is_active', 'اكتب 1 للنشط أو 0 لغير النشط'],
            ['image', 'أدرج الصورة داخل هذا العمود وفي صف المنتج نفسه'],
        ];

        $guide->fromArray($guideData, null, 'A1');

        $guide->getStyle('A1:B1')->getFont()
            ->setBold(true)
            ->setSize(12);

        $guide->getStyle('A1:B1')->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setARGB('FFD9EAF7');

        $guide->getStyle('A1:B21')->getAlignment()
            ->setVertical('center')
            ->setWrapText(true);

        $guide->getColumnDimension('A')->setWidth(28);
        $guide->getColumnDimension('B')->setWidth(65);
        $guide->freezePane('A2');

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            'product_import_template.xlsx',
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    public function store(
        Request $request,
        ProductExcelImporter $importer
    ) {
        $request->validate([
            'excel_file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:20480',
            ],
        ]);

        $result = $importer->import(
            $request->file('excel_file')->getRealPath()
        );

        return back()->with(
            'success',
            'تم الاستيراد بنجاح. '
            . 'منتجات جديدة: ' . $result['created']
            . ' | منتجات محدثة: ' . $result['updated']
            . ' | خيارات ومقاسات: ' . $result['variants']
            . ' | صور: ' . $result['images']
        );
    }
}