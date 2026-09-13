<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    protected $headings;

    function __construct()
    {
        $this->headings = [
            'id',
            'name',
            'slug',
            'category_id',
            'subcategory_id',
            'brand_id',
            'regular_price',
            'purchase_price',
            'discount',
            'discount_type',
            'wholesale_price',
            'stock',
            'sku',
            'barcode',
            'unit',
            'low_stock_qty',
            'thumbnail_img',
            'photos',
            'tags',
            'short_description',
            'description',
            'status',
            'is_published',
            'is_featured',
            'best_selling',
            'is_new_arrival',
            'todays_deal',
            'is_variant',
            'video_link',
            'badge_name',
            'batch_no',
            'shipping_type',
            'shipping_cost',
            'weight',
            'length',
            'width',
            'height',
            'meta_title',
            'meta_description',
            'meta_img',
            'position',
            'variant_attributes',
        ];
    }

    public function collection()
    {
        return Product::with('category', 'subcategory', 'brand', 'productImages', 'colors', 'sizes')->latest()->get();
    }

    public function map($product): array
    {
        // Build photos JSON
        $photos = [];
        if ($product->productImages && $product->productImages->count() > 0) {
            foreach ($product->productImages as $image) {
                if ($image->gallery_image) {
                    $photos[] = asset('/galleryImage/' . $image->gallery_image);
                }
            }
        }
        $photosJson = !empty($photos) ? json_encode(['photos' => $photos], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        // Build variant attributes JSON
        $variantAttributes = [];
        if ($product->is_variable == 1 && $product->productImages && $product->productImages->count() > 0) {
            foreach ($product->productImages as $image) {
                $variantAttributes[] = [
                    'attributes' => $image->size ?? '',
                    'price' => (string)($image->price ?? $product->regular_price),
                    'wholesale_price' => (string)($image->wholesale_price ?? $product->wholesale_price),
                    'sku' => $image->sku ?? '',
                    'quantity' => (string)($image->qty ?? $product->qty),
                    'image' => asset('/galleryImage/' . $image->gallery_image) ?? '',
                ];
            }
        }
        $variantJson = !empty($variantAttributes) ? json_encode(['variant_attributes' => $variantAttributes], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        // Build tags
        $tags = [];
        if (isset($product->tags) && $product->tags) {
            $tags = array_map('trim', explode(',', $product->tags));
        }
        $tagsString = implode(',', $tags);

        return [
            $product->id ?? '',
            $this->ensureUtf8($product->name),
            $this->ensureUtf8($product->slug),
            $product->cat_id ?? '',
            $product->sub_cat_id ?? '',
            $product->brand_id ?? '',
            $product->regular_price ?? '',
            $product->buy_price ?? '',
            $product->regular_price - $product->discount_price ?? '',
            'flat',
            $product->wholesale_price ?? '',
            $product->stock ?? '',
            $product->sku ?? '',
            '', // barcode
            'Pc', // unit
            '', // low_stock_qty
            $product->image ? asset('/product/images/' . $product->image) : '',
            $photosJson,
            $tagsString,
            $this->ensureUtf8($product->short_description),
            $this->ensureUtf8($product->long_description),
            $product->status ?? 1,
            1, // is_published
            0, // is_featured
            0, // best_selling
            0, // is_new_arrival
            0, // todays_deal
            $product->is_variable ?? 0,
            '', // video_link
            '', // badge_name
            '', // batch_no
            'flat_rate', // shipping_type
            $product->inside_dhaka ?? '', // shipping_cost
            '', // weight
            '', // length
            '', // width
            '', // height
            $this->ensureUtf8($product->seo_title ?? ''),
            $this->ensureUtf8($product->seo_description ?? ''),
            '', // meta_img
            $product->priority ?? '', // position
            $variantJson,
        ];
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\n",
            'use_bom' => true,
            'include_separator_line' => false,
            'excel_compatibility' => true,
        ];
    }

    /**
     * Ensure proper UTF-8 encoding
     */
    private function ensureUtf8($string)
    {
        if (is_null($string) || $string === '') {
            return $string;
        }

        if (mb_check_encoding($string, 'UTF-8')) {
            return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        }

        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'];

        foreach ($encodings as $encoding) {
            if (mb_check_encoding($string, $encoding)) {
                $converted = mb_convert_encoding($string, 'UTF-8', $encoding);
                if (mb_check_encoding($converted, 'UTF-8')) {
                    return $converted;
                }
            }
        }

        $encoded = mb_convert_encoding($string, 'UTF-8', 'auto');
        return $encoded ?: $string;
    }
}
