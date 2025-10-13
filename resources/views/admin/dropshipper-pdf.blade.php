<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="X-UA-Compatible" content="IE-edge" />
    <title>Invoice - A4 (3 Copies)</title>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    />

    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: "Open Sans", sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            background: #fff;
            padding: 10mm;
            box-sizing: border-box;
        }

        .invoice-copy {
            border: 2px solid #f48fb1;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 15px;
            height: calc(297mm / 3 - 20px);
            overflow: hidden;
            position: relative;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f48fb1;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .invoice-header img {
            max-height: 70px;
            border-radius: 8px;
        }

        .invoice-header p.note {
            font-size: 13px;
            color: #c2185b;
            font-weight: 600;
            margin: 0;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .info-box {
            width: 32%;
        }

        .info-box h6 {
            font-weight: 700;
            border-bottom: 1px solid #f48fb1;
            padding-bottom: 3px;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .info-box p {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        table.invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .invoice-table th {
            background-color: #fce4ec;
            border: 1px solid #f48fb1;
            font-size: 13px;
            text-align: left;
            padding: 5px;
        }

        .invoice-table td {
            border: 1px solid #ddd;
            font-size: 12px;
            padding: 4px 5px;
        }

        .totals td {
            border: none !important;
            font-size: 13px;
            text-align: right;
            padding: 2px;
        }

        .totals tr:last-child td {
            font-weight: 700;
            color: #c2185b;
            font-size: 14px;
        }

        .copy-label {
            position: absolute;
            bottom: 4px;
            right: 8px;
            font-size: 11px;
            color: #aaa;
            font-style: italic;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                margin: 0;
            }

            .page {
                margin: 0;
                padding: 5mm;
                page-break-after: always;
            }

            .invoice-copy {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
@foreach ($selectedOrders as $order)
    <div class="page">
        @for ($i = 1; $i <= 3; $i++)
            <div class="invoice-copy">
                <div class="invoice-header">
                    <img src="{{ $order->dropshipper->image }}" alt="logo" />
                    <p class="note">
                        আগে পণ্য দেখে নিন, তারপর ডেলিভারি ম্যানকে টাকা দিন।
                    </p>
                </div>

                <div class="info-section">
                    <div class="info-box">
                        <h6>Customer Info</h6>
                        <p>{{ $order->name }}</p>
                        <p>{{ $order->phone }}</p>
                        <p>{{ $order->address }}</p>
                    </div>

                    <div class="info-box">
                        <h6>Company Info</h6>
                        <p>{{ $order->dropshipper->domain_name }}</p>
                        <p>Call: {{ $order->dropshipper->phone }}</p>
                    </div>

                    <div class="info-box">
                        <h6>Order Info</h6>
                        <p><strong>Order #: </strong>{{ $order->orderId }}</p>
                        @if ($order->courier_name == 'Pathao')
                            <p><strong>Courier: </strong>Pathao → {{ $order->pathao_city_name }} → {{ $order->pathao_zone_name }}</p>
                        @endif
                    </div>
                </div>

                <table class="invoice-table">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Size</th>
                        <th>Color</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $sum = 0; @endphp
                    @foreach($order->orderDetails as $orderDetails)
                        @php $total = $orderDetails->price * $orderDetails->qty; $sum += $total; @endphp
                        <tr>
                            <td>{{ $orderDetails->product->name }}</td>
                            <td>{{ $orderDetails->qty }}</td>
                            <td>{{ $total }} Tk.</td>
                            <td>{{ $orderDetails->size ?? '-' }}</td>
                            <td>{{ $orderDetails->color ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <table class="totals" width="100%">
                    <tr>
                        <td><strong>Subtotal:</strong></td>
                        <td>{{ $sum }} Tk.</td>
                    </tr>
                    <tr>
                        <td><strong>Discount:</strong></td>
                        <td>{{ $order->discount ?? 0 }} Tk.</td>
                    </tr>
                    <tr>
                        <td><strong>Delivery Charge:</strong></td>
                        <td>{{ $order->area }} Tk.</td>
                    </tr>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td>{{ $order->price }} Tk.</td>
                    </tr>
                </table>

                <div class="copy-label">
                    @if($i==1) Customer Copy
                    @elseif($i==2) Office Copy
                    @else Delivery Copy
                    @endif
                </div>
            </div>
        @endfor
    </div>
@endforeach

<script>
    window.onload = function () {
        window.print();
    };
</script>
</body>
</html>
