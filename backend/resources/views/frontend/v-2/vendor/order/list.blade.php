@extends('frontend.v-2.vendor.master')

@section('content')
<!-- Page header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">Products</h4>
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
                        <h3 class="card-title">Order List</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">SL</th>
                                    <th width="10%">Name</th>
                                    <th width="10%">D.Price</th>
                                    <th width="10%">R.Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderDetails as $order)
                                    <tr>
                                        <td>{{ $loop->index+1 }}</td>
                                        <td>
                                            <img src="{{ asset('/product/images/'.$order->product->image) }}" height="50" width="50" />
                                            {{ $order->product->name }}
                                        </td>
                                        <td>{{ $order->product->discount_price }} Tk.</td>
                                        <td>{{ $order->product->regular_price }} Tk.</td>
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