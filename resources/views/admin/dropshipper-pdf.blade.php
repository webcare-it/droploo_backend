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
            margin: 10px;
            background: #fff;
        }

        .invoice-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            page-break-after: auto;
        }

        .invoice-box {
            width: 48%; /* 2 invoices per row */
            border: 2px solid #f48fb1;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            page-break-inside: avoid;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px dashed #f48fb1;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .invoice-header img {
            max-height: 50px;
            width: auto;
            border-radius: 6px;
        }

        .invoice-header p.note {
            font-weight: 600;
            font-size: 12px;
            background: #ffe4ec;
            color: #c2185b;
            padding: 5px 8px;
            border-radius: 5px;
            text-align: right;
            margin: 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-box {
            width: 48%;
        }

        .info-box h6 {
            font-weight: 700;
            border-bottom: 1px solid #f48fb1;
            margin-bottom: 3px;
            font-size: 13px;
        }

        .info-box p {
            font-size: 13px;
            font-weight: 600;
            margin: 0;
            line-height: 1.3;
        }

        table.invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .invoice-table th {
            background-color: #fce4ec;
            border: 1px solid #f48fb1;
            font-size: 13px;
            font-weight: 700;
            text-align: left;
            padding: 3px 5px;
        }

        .invoice-table td {
            border: 1px solid #ddd;
            font-size: 13px;
            padding: 3px 5px;
        }

        .totals {
            width: 100%;
            font-size: 13px;
        }

        .totals td {
            text-align: right;
            padding: 2px 3px;
            font-weight: 600;
        }

        .totals tr:last-child td {
            font-size: 13px;
            color: #c2185b;
            font-weight: 700;
        }

        .print-info {
            background-color: #fff3e0;
            border: 1px solid #ffb74d;
            border-radius: 5px;
            padding: 8px;
            margin-top: 10px;
            font-size: 12px;
            font-weight: 600;
            color: #e65100;
        }

        /* ✅ Print settings: no auto-fit, no page breaks, 4 invoices per page */
        @media print {
            body {
                margin: 5mm;
                zoom: 1;
            }

            @page {
                size: auto;
                margin: 5mm;
            }

            .invoice-box {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>
<div class="invoice-container">
    @foreach ($selectedOrders as $order)
        <div class="invoice-box">
            <div class="invoice-header">
                <img src="{{ $order->dropshipper->image }}" alt="logo" />
                <p class="note">
                    আগে পণ্য দেখে নিন, তারপর টাকা দিন।
                </p>
            </div>

            <!-- ✅ Customer Info Left + Company Info Right -->
            <div class="info-row">
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
            </div>

            <!-- Order Info Below -->
            <div class="info-row">
            <div class="info-box">
                <h6>Order Info</h6>
                <p><strong>#:</strong> {{ $order->orderId }}</p>
                @if ($order->courier_name == 'Pathao')
                    <p><strong>Courier:</strong> Pathao → {{ $order->pathao_city_name }} → {{ $order->pathao_zone_name }}</p>
                @endif
            </div>
            @if ($order->consignmentId != null)
                <div class="info-box">
                    <h6 style="font-size: 25px;">Steadfast Parcel ID</h6>
                    <p style="font-size: 25px;"><strong>#:</strong> {{ $order->consignmentId }}</p>
                </div>
            @endif
            </div>

            <table class="invoice-table">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>৳</th>
                </tr>
                </thead>
                <tbody>
                @php $sum = 0; @endphp
                @foreach($order->orderDetails as $orderDetails)
                    @php $total = $orderDetails->price * $orderDetails->qty; $sum += $total; @endphp
                    <tr>
                        <td>
                            {{ $orderDetails->product->name }}
                            @if(!empty($orderDetails->color) || !empty($orderDetails->size))
                                <br>
                                <small style="color:#c2185b;">
                                    @if(!empty($orderDetails->color))
                                        Color: {{ $orderDetails->color }}
                                    @endif
                                    @if(!empty($orderDetails->size))
                                        @if(!empty($orderDetails->color)) | @endif
                                        Size: {{ $orderDetails->size }}
                                    @endif
                                </small>
                            @endif
                        </td>
                        <td>{{ $orderDetails->qty }}</td>
                        <td>{{ $total }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <table class="totals">
                <tr>
                    <td>Subtotal:</td>
                    <td>{{ $sum }} Tk.</td>
                </tr>
                <tr>
                    <td>Discount:</td>
                    <td><span class="text-danger">-</span>{{ $order->discount }} Tk.</td>
                </tr>
                <tr>
                    <td>Delivery:</td>
                    <td>{{ $order->area }} Tk.</td>
                </tr>
                <tr>
                    <td>Total:</td>
                    <td>{{ $order->price }} Tk.</td>
                </tr>
            </table>

            <!-- Print Date & Time Section -->
            <div class="print-info">
                <div><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</div>
                <div id="printDateTime{{ $loop->index }}"></div>
            </div>
        </div>
    @endforeach
</div>

<script>
    window.onload = function() {
        window.print();
    };
    
    // Display print date and time
    document.addEventListener('DOMContentLoaded', function() {
        const printDateTime = new Date();
        const formattedDate = printDateTime.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        const formattedTime = printDateTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const dateTimeText = 'Print Date: ' + formattedDate + ' | Print Time: ' + formattedTime;
        
        // Populate all print date/time boxes
        const printBoxes = document.querySelectorAll('[id^="printDateTime"]');
        printBoxes.forEach(function(box) {
            box.textContent = dateTimeText;
        });
    });
</script>
</body>
</html>
