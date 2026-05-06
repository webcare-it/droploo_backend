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
                        <h3 class="card-title">Product List</h3>
                    </div>
                    <a href="{{url('/vendor/product/upload')}}" class="btn btn-success">Add New</a>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">SL</th>
                                    <th width="10%">Name</th>
                                    <th width="10%">Category Name</th>
                                    <th width="10%">D.Price</th>
                                    <th width="10%">R.Price</th>
                                    <th width="10%">Status</th>
                                    <th width="10%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $loop->index+1 }}</td>
                                        <td>
                                            <img src="{{ asset('/product/images/'.$product->image) }}" height="50" width="50" />
                                            {{ $product->name }}
                                        </td>
                                        <td>{{ $product->category->name ?? 'No category name found' }}</td>
                                        <td>{{ $product->discount_price }} Tk.</td>
                                        <td>{{ $product->regular_price }} Tk.</td>
                                        <td>
                                            @if($product->status == 0)
                                                <span class="badge rounded-pill bg-danger">Inactive</span>
                                            @else
                                                <span class="badge rounded-pill bg-primary">Active</span>
                                            @endif

                                        </td>
                                        <td>
                                            <a href="{{url('/supplier/product/edit/'.$product->id.'/'.$product->slug)}}" class="badge rounded-pill bg-info">
                                                Edit
                                            </a>
                                            {{-- <a href="" onclick="return confirm('Are you sure delete this product ?')" class="badge rounded-pill bg-danger">
                                                Delete
                                            </a> --}}
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