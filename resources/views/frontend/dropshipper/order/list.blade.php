@extends('frontend.dropshipper.master')

@section('content')
<!-- Page header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">{{$orderType}} Orders</h4>
            </div>
        </div>
    </div>
</div>
<!-- Page header -->

<!-- Page Content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Order History</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Transfer Date</th>
                                    <th>OrderId</th>
                                    <th>Dropshipping OrderId</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Charge</th>
                                    <th>Amount</th>
                                    <th>Profit</th>
                                    <th>Is Paid?</th>
                                    <th>Track Order</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <td>{{$order->created_at ?? ''}}</td>
                                    <td>{{$order->orderId ?? ''}}</td>
                                    <td>{{$order->dropshipperOrderId ?? ''}}</td>
                                    <td>
                                        Name: {{$order->name ?? ''}}<br>
                                        Phone: {{$order->phone ?? ''}}<br>
                                        Address: {{$order->address ?? ''}}<br>
                                    </td>
                                    <td>
                                        @foreach ($order->orderDetails as $details)
                                            {{ $details->qty ?? 'No name found' }}X
                                            {{ $details->product?->name }}<br />
                                        @endforeach
                                    </td>
                                    <td>{{$order->area?? 'Not Found'}}</td>
                                    <td>{{$order->price?? 'Not Found'}}</td>
                                    @php
                                        $detailsToPay = App\Models\Order::where('id', $order->id)->with('orderDetails')->first();
                                        $totalWholeSalePrice = 0;

                                        foreach ($detailsToPay->orderDetails as $product) {
                                            if ($product->product) {  // Check if the product exists
                                            $totalWholeSalePrice += $product->product->wholesale_price * $product->qty;
                                            }
                                        }
                                        $payableAmount = $order->price - $totalWholeSalePrice;
                                    @endphp

                                    <td>
                                        {{$payableAmount}}
                                    </td>
                                    <td>
                                        @if ($order->is_dpaid == 1)
                                            <a class="btn btn-success">Yes</a>
                                        @else
                                        <a class="btn btn-danger">No</a>    
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->tracking_link != null)
                                            <a href="{{$order->tracking_link}}" target="_blank" class="btn btn-info">Track</a>
                                        @else
                                        <a class="btn btn-info">Not Found</a>    
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->order_status == 'complete')
                                        shipment
                                        @else
                                        {{$order->order_status}}   
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<!-- Page Content -->
@endsection

@push('script')
<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
@endpush