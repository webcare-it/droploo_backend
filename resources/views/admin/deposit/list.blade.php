@extends('admin.master')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Deposit History</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Dropshipper</th>
                                    <th>Website</th>
                                    <th>Amount</th>
                                    <th>Payment Gateway</th>
                                    <th>TID</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deposits as $deposit)
                                <tr>
                                    <td>{{$deposit->created_at ?? ''}}</td>
                                    <td>{{$deposit->dropshipper->name ?? ''}}</td>
                                    <td>{{$deposit->dropshipper->domain_name ?? ''}}</td>
                                    <td>{{$deposit->amount?? 'Not Found'}}</td>
                                    <td>{{$deposit->payment_gateway?? 'Not Found'}}</td>
                                    <td>{{$deposit->transaction_id?? 'Not Found'}}</td>
                                    <td>
                                        @if ($deposit->status == 0)
                                        <a class="btn btn-primary">Pending</a>
                                        @elseif ($deposit->status == 1)
                                        <a class="btn btn-success">Approved</a>
                                        @elseif ($deposit->status == 2)
                                        <a class="btn btn-danger">Rejected</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($deposit->status == 0)
                                        <a href="{{url('approve-deposit/'.$deposit->id)}}" class="btn btn-success" onclick="return confirm('Are you sure?')">Approve</a>
                                        <a href="{{url('reject-deposit/'.$deposit->id)}}" class="btn btn-danger" onclick="return confirm('Are you sure?')">Reject</a>
                                        @else
                                        @if ($deposit->status == 1)
                                        <a class="btn btn-success">Approved</a>
                                        @elseif ($deposit->status == 2)
                                        <a class="btn btn-danger">Rejected</a>
                                        @endif
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