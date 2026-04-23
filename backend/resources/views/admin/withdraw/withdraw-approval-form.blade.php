@extends('admin.master')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Withdraw Approval</h3>
                        </div>
                        <div class="card-body">
                            <a href="{{ url('/dropshipper-withdraw-requests') }}" class="btn btn-primary">Back</a>
                            <form action="{{ url('/dropshipper-withdraw-approve') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="request_id" value="{{ $withdrawRequest->id }}">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="rules_conditions">Dropshipper Name</label>
                                        <input type="text" name="name"
                                            value="{{ $withdrawRequest->dropshipper->name }}" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="rules_conditions">Dropshipper Phone</label>
                                        <input type="text" name="phone"
                                            value="{{ $withdrawRequest->dropshipper->phone }}" class="form-control"
                                            readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="rules_conditions">Amount</label>
                                        <input type="text" name="amount" value="{{ $withdrawRequest->amount }}"
                                            class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="rules_conditions">Transaction Proof (Optional)</label>
                                        <input type="file" name="transaction_image" id="transaction_image"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Approve</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
