<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FacebookCatalogExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with('category')->where('status', 1)->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'title',
            'description',
            'availability',
            'condition',
            'price',
            'link',
            'image_link',
            'brand',
            'item_group_id',
            'google_product_category',
            'product_type',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            strip_tags($product->short_description ?? $product->long_description ?? 'N/A'),
            'in stock',
            'new',
            ($product->discount_price ?? $product->regular_price) . ' BDT',
            url('/product/' . $product->slug),
            $product->imageUrl ?? '',
            'Sohojlobbo',
            $product->id,
            'Apparel & Accessories > Clothing > Outerwear > Scarves & Shawls',
            optional($product->category)->name ?? 'General',
        ];
    }
}
