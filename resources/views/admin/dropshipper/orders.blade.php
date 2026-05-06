@extends('admin.master')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{$dropshipper->domain_name}} Orders</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <td>{{$order->created_at ?? ''}}</td>
                                    <td><span style="font-size: 16px; font-weight:600;">{{ $order->orderId ?? 'No order id found' }}</span></td>
                                    <td>
                                        {{ $order->name?? 'No name found' }}<br/>
                                        <span style="color: green">{{ $order->phone?? 'No phone found' }}</span><br/>
                                        {{ substr($order->address,0,70)?? 'No address found' }} <br/>
                                        <span class="badge rounded-pill {{ $order->customer_type == 'Old Customer' ? 'bg-danger' : 'bg-success' }}">{{ $order->customer_type }}</span> <br/>
                                    </td>
                                    <td>
                                        @foreach ($order->orderDetails as $details)
                                            {{ $order->qty?? ' ' }}X {{ $details->product?->name }}<br/>
                                            {{ 'Size: ' . $details->size?? '' }} | {{ 'Color: ' . $details->color?? '' }}
                                        @endforeach
                                    </td>
                                    <td>
                                        <b>Amount :</b> {{ $order->price }} Tk. <br/>
                                        <b>Delivery :</b> {{ $order->area }} Tk.
                                    </td>
                                    <td>
                                        <a href="javascript:;" class="btn btn-warning">
                                            {{ucfirst($order->order_status)}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ url('/order/view/' . $order->id) }}" class="btn btn-info">Details</a>
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
</div>
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