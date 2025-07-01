@extends('frontend.dropshipper.master')

@section('content')
<!-- Page header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">Withdraws</h4>
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
                        <h3 class="card-title">Withdraw History</h3>
                    </div>
                    <a href="{{url('/dropshipper/make-withdraw')}}" class="btn btn-success">Make Withdraw Request</a>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Transaction Proof</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($withdraws as $withdraw)
                                <tr>
                                    <td>{{$withdraw->created_at ?? ''}}</td>
                                    <td>{{$withdraw->amount?? 'Not Found'}}</td>
                                    <td>
                                        @if ($withdraw->status == 'Pending')
                                        <a class="btn btn-primary">Pending</a>
                                        @elseif ($withdraw->status == 'Processing')
                                        <a class="btn btn-warning">Processing</a>
                                        @elseif ($withdraw->status == 'Approved')
                                        <a class="btn btn-success">Approved</a>
                                        @elseif ($withdraw->status == 'Rejected')
                                        <a class="btn btn-danger">Rejected</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($withdraw->transaction_image != null)
                                        <img src="{{asset('frontend/assets/images/transaction/'.$withdraw->transaction_image)}}" height="100" width="100">
                                        @else
                                        Not Found
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