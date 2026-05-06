@extends('frontend.dropshipper.master')

@section('content')
<!-- Page header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">Deposits</h4>
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
                        <h3 class="card-title">Deposit History</h3>
                    </div>
                    <a href="{{url('/dropshipper/make-deposit')}}" class="btn btn-success">Make Deposit</a>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment Gateway</th>
                                    <th>TID</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deposits as $deposit)
                                <tr>
                                    <td>{{$deposit->created_at ?? ''}}</td>
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