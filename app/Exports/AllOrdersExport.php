<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class AllOrdersExport implements FromQuery, WithHeadings, WithMapping, WithCustomCsvSettings
{
    protected $query;

    function __construct(Builder $query) {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function map($order) : array {
        $products = [];
        foreach ($order->orderDetails as $detail) {
            $attr = [];
            if ($detail->size && $detail->size !== 'No size') {
                $attr['attribute'] = $detail->size;
            }
            if ($detail->color && $detail->color !== 'No color') {
                $attr['attribute'] = ($attr['attribute'] ?? '') . ' ' . $detail->color;
            }

            $products[] = [
                'product_id' => $detail->product_id,
                'name' => $detail->product?->name ?? '',
                'quantity' => $detail->qty,
                'price' => $detail->price,
                'attribute_value' => !empty($attr) ? $attr : null,
            ];
        }

        $paymentType = $this->mapPaymentType($order->payment_type);
        $paymentStatus = $this->mapPaymentStatus($order);

        return [
            $order->orderId ?? '',
            $this->ensureUtf8($order->name),
            $this->ensureUtf8($order->email ?? ''),
            $order->phone,
            $this->ensureUtf8($order->address),
            json_encode($products, JSON_UNESCAPED_UNICODE),
            $paymentType,
            $order->order_status,
            $paymentStatus,
            $this->ensureUtf8($order->notes ?? ''),
            $this->ensureUtf8($order->pathao_zone_name ?? $order->area ?? ''),
            $order->order_type == 'Dropshipping' ? 'Dropshipping' : 'own',
        ];
    }

    private function mapPaymentType($type)
    {
        $map = [
            'cod' => 'cash_on_delivery',
            'cash_on_delivery' => 'cash_on_delivery',
            'wallet' => 'wallet',
            'online' => 'online',
            'bkash' => 'bkash',
            'nagad' => 'nagad',
            'rocket' => 'rocket',
        ];
        return $map[strtolower($type)] ?? $type;
    }

    private function mapPaymentStatus($order)
    {
        if ($order->order_status === 'delivered' || $order->order_status === 'complete' || $order->order_status === 'paid') {
            return 'paid';
        }
        if ($order->advance && (float)$order->advance > 0) {
            return 'partial';
        }
        return 'unpaid';
    }

    public function headings() : array {
        return [
            'order_code',
            'customer_name',
            'customer_email',
            'customer_phone',
            'shipping_address',
            'products',
            'payment_type',
            'delivery_status',
            'payment_status',
            'notes',
            'shipping_area',
            'order_by',
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
