@extends('admin.master')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dropshippers</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Website</th>
                                    <th>Phone</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dropshippers as $dropshipper)
                                <tr>
                                    <td>{{$dropshipper->created_at ?? ''}}</td>
                                    <td>{{$dropshipper->name ?? ''}}</td>
                                    <td>{{$dropshipper->domain_name ?? ''}}</td>
                                    <td>{{$dropshipper->phone ?? ''}}</td>
                                    <td>
                                        <a href="{{url('dropshipper-details/'.$dropshipper->id)}}" class="btn btn-info">Details</a>
                                        <a href="{{url('dropshipper-edit/'.$dropshipper->id)}}" class="btn btn-primary">Edit</a>
                                        <a href="{{url('dropshipper-delete/'.$dropshipper->id)}}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this dropshipper?')">Delete</a>
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
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "ordering": true,  // Ensure sorting is enabled on all columns
            "stateSave": true, // Preserve state between page reloads
            "order": []  // Use default sorting behavior as provided by the controller (no custom ordering)
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
@endpush
