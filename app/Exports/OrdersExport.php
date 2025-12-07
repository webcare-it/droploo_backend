<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    protected $orderIds;
    protected $selectedColumns;

    function __construct($orderIds, $selectedColumns = null) {
        $this->orderIds = $orderIds;
        $this->selectedColumns = $selectedColumns ?: [
            'ItemType(*)',
            'StoreName(*)',
            'OrderId',
            'RecipientName(*)',
            'RecipientPhone(*)',
            'RecipientCity(*)',
            'RecipientZone(*)',
            'RecipientArea',
            'RecipientAddress(*)',
            'AmountToCollect(*)',
            'ItemQuantity(*)',
            'ItemWeight(*)',
            'ItemDesc',
            'SpecialInstruction',
            'OrderStatus', // Added Order Status column
        ];
    }

    public function collection()
    {
       return Order::with('orderDetails', 'admin')->whereIn('id', $this->orderIds)->get();
    }

    public function map($order) : array {
        $columns = [];
        
        $productNames = [];
        foreach($order->orderDetails as $details){
            if ($details->product) {
                $productNames[] = $this->ensureUtf8($details->product->name);
            }
        }
        $combinedProductNames = implode(', ', $productNames);
        
        $data = [
            'ItemType(*)' => 'parcel',
            'StoreName(*)' => 'droploo.com',
            'OrderId' => $order->orderId,
            'RecipientName(*)' => $this->ensureUtf8($order->name),
            'RecipientPhone(*)' => $order->phone,
            'RecipientCity(*)' => $this->ensureUtf8($order->pathao_city_name),
            'RecipientZone(*)' => $this->ensureUtf8($order->pathao_zone_name),
            'RecipientArea' => '1',
            'RecipientAddress(*)' => $this->ensureUtf8($order->address),
            'AmountToCollect(*)' => $order->price,
            'ItemQuantity(*)' => '1',
            'ItemWeight(*)' => '0.5',
            'ItemDesc' => $this->ensureUtf8($combinedProductNames),
            'SpecialInstruction' => $this->ensureUtf8($order->notes), // Changed from pathao_special_note to notes
            'OrderStatus' => $this->ensureUtf8($order->order_status), // Added Order Status column
        ];
        
        // Filter data based on selected columns
        foreach ($this->selectedColumns as $column) {
            $columns[] = $data[$column] ?? '';
        }
        
        return $columns;
    }

    public function headings() : array {
        return $this->selectedColumns;
    }
    
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\n",
            'use_bom' => true, // Add BOM for UTF-8 support
            'include_separator_line' => false,
            'excel_compatibility' => true, // Enable Excel compatibility for better UTF-8 support
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
        
        // If it's already a valid UTF-8 string, return as is
        if (mb_check_encoding($string, 'UTF-8')) {
            // Also ensure it's properly normalized
            return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        }
        
        // Try different encodings that might contain Bangla text
        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'];
        
        foreach ($encodings as $encoding) {
            if (mb_check_encoding($string, $encoding)) {
                $converted = mb_convert_encoding($string, 'UTF-8', $encoding);
                if (mb_check_encoding($converted, 'UTF-8')) {
                    return $converted;
                }
            }
        }
        
        // If all else fails, try auto detection
        $encoded = mb_convert_encoding($string, 'UTF-8', 'auto');
        return $encoded ?: $string;
    }
}