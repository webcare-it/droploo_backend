<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $orderIds;
    protected $selectedColumns;

    function __construct($orderIds, $selectedColumns = null) {
        $this->orderIds = $orderIds;
        $this->selectedColumns = $selectedColumns ?: [
            'ItemType(*)',
            'StoreName(*)',
            'MerchantOrderId',
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
                $productNames[] = $details->product->name;
            }
        }
        $combinedProductNames = implode(', ', $productNames);
        
        $data = [
            'ItemType(*)' => 'parcel',
            'StoreName(*)' => 'droploo.com',
            'MerchantOrderId' => $order->orderId,
            'RecipientName(*)' => $order->name,
            'RecipientPhone(*)' => $order->phone,
            'RecipientCity(*)' => $order->pathao_city_name,
            'RecipientZone(*)' => $order->pathao_zone_name,
            'RecipientArea' => '1',
            'RecipientAddress(*)' => $order->address,
            'AmountToCollect(*)' => $order->price,
            'ItemQuantity(*)' => '1',
            'ItemWeight(*)' => '0.5',
            'ItemDesc' => $combinedProductNames,
            'SpecialInstruction' => $order->notes, // Changed from pathao_special_note to notes
            'OrderStatus' => $order->order_status, // Added Order Status column
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
}