<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\ShouldChunk;

class AllOrdersExport implements FromQuery, WithHeadings, WithMapping, WithCustomCsvSettings, ShouldChunk
{
    protected $query;

    function __construct(Builder $query) {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function map($order) : array {
        $productNames = [];
        foreach($order->orderDetails as $details){
            if ($details->product) {
                $productNames[] = $this->ensureUtf8($details->product->name);
            }
        }
        $combinedProductNames = implode(', ', $productNames);

        return [
            $order->orderId ?? '',
            $this->ensureUtf8($order->name),
            $order->phone,
            $this->ensureUtf8($order->email ?? ''),
            $this->ensureUtf8($order->address),
            $this->ensureUtf8($order->pathao_city_name ?? ''),
            $this->ensureUtf8($order->pathao_zone_name ?? ''),
            $order->price,
            $order->area,
            $order->discount ?? '',
            $order->advance ?? '',
            $order->qty,
            $order->payment_type,
            $this->ensureUtf8($order->order_status),
            $this->ensureUtf8($order->order_type),
            $this->ensureUtf8($order->customer_type),
            $this->ensureUtf8($order->courier_name ?? ''),
            $this->ensureUtf8($combinedProductNames),
            $this->ensureUtf8($order->admin?->name ?? ''),
            $order->created_at ? $order->created_at->format('d-m-Y H:i:s') : '',
        ];
    }

    public function headings() : array {
        return [
            'Order ID',
            'Customer Name',
            'Phone',
            'Email',
            'Address',
            'City',
            'Zone',
            'Amount',
            'Delivery Charge',
            'Discount',
            'Advance',
            'Quantity',
            'Payment Type',
            'Order Status',
            'Order Type',
            'Customer Type',
            'Courier',
            'Product(s)',
            'Assigned User',
            'Order Date',
        ];
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
