<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="X-UA-Compatible" content="IE-edge" />
    <title>Invoice</title>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    />

    <style>
        body {
            font-family: "Open Sans", sans-serif;
            color: #000;
            margin: 25px;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #f48fb1;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .invoice-header img {
            max-height: 90px;
            border-radius: 10px;
        }

        .invoice-header p.note {
            font-weight: 600;
            font-size: 16px;
            background: #ffe4ec;
            color: #c2185b;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .info-box {
            width: 32%;
        }

        .info-box h6 {
            font-weight: 700;
            border-bottom: 2px solid #f48fb1;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .info-box p {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        table.invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-table th {
            background-color: #fce4ec;
            border: 1px solid #f48fb1;
            font-size: 16px;
            font-weight: 700;
            text-align: left;
            padding: 10px;
        }

        .invoice-table td {
            border: 1px solid #ddd;
            font-size: 15px;
            padding: 8px 10px;
        }

        .invoice-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .totals td {
            border: none !important;
            text-align: right;
            font-weight: 600;
        }

        .totals tr:last-child td {
            font-size: 18px;
            font-weight: 700;
            color: #c2185b;
        }

        hr {
            border-top: 2px dashed #f48fb1;
            margin: 40px 0;
        }

        @media print {
            body {
                margin: 10mm;
            }

            .invoice-header p.note {
                background: none;
                color: #c2185b;
            }

            hr {
                page-break-after: always;
                border-top: 1px dashed #bbb;
            }
        }
    </style>
</head>

<body>
@foreach ($selectedOrders as $order)
    <div class="invoice-header">
        <div>
            <img src="{{ $order->dropshipper->image }}" alt="logo" />
        </div>
        <div style="text-align:right">
            <p class="note">
                আগে পণ্য দেখে নিন, তারপর ডেলিভারি ম্যানকে টাকা দিন।
            </p>
        </div>
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

    <hr />
@endforeach

<script>
    window.onload = function () {
        window.print();
    };
</script>
</body>
</html>
