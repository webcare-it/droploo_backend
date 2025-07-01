@extends('admin.master')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Withdraw Requests</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Dropshipper</th>
                                    <th>Dropshipper Name</th>
                                    <th>Amount</th>
                                    <th>Banking Info</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($withdrawRequests as $request)
                                <tr>
                                    <td>{{$request->created_at ?? 'N.A'}}</td>
                                    <td>{{$request->dropshipper->name}}</td>
                                    <td>{{$request->dropshipper->phone}}</td>
                                    <td>{{$request->amount ?? 'N.A'}}</td>
                                    <td>
                                        Account Type: {{$request->dropshipper->bankInfo->banking_type??'Not Found'}}</br>
                                        Account Info: {{$request->dropshipper->bankInfo->account_details??'Not Found'}}
                                    </td>
                                    <td>
                                        @if ($request->status == 'Pending')
                                        <a class="btn btn-danger">{{$request->status ?? 'N.A'}}</a>
                                        @elseif ($request->status == 'Processing')
                                        <a class="btn btn-warning">{{$request->status ?? 'N.A'}}</a>
                                        @elseif ($request->status == 'Approved')
                                        <a class="btn btn-success">{{$request->status ?? 'N.A'}}</a>
                                        @elseif ($request->status == 'Rejected')
                                        <a class="btn btn-danger">{{$request->status ?? 'N.A'}}</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($request->status == 'Pending')
                                        <a href="{{url('dropshipper-withdraw-approve/form/'.$request->id)}}" class="btn btn-success">Approve</a>
                                        <a href="{{url('dropshipper-withdraw-reject/'.$request->id)}}" class="btn btn-danger" onclick="return confirm('Are you sure?')">Reject</a>
                                        @elseif ($request->status == 'Approved')
                                        <a class="btn btn-success">Approved</a>
                                        @elseif ($request->status == 'Rejected')
                                        <a class="btn btn-danger">Rejected</a>
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