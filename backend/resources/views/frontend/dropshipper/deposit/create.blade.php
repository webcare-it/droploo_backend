@extends('frontend.dropshipper.master')

@section('content')
    <!-- Page header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Make Deposit</h4>
                </div>
            </div>
        </div>
    </div>
    <!-- Page header -->

    <!-- Page Content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Bkash (Merchant)</h5>
                            <p class="card-text">
                                bKash Details:<br />
                                A/C- <b>01814812233</b>(Merchant)<br />
                                *How to make payment?<br />
                                01. Go to bKash Menu by dialing *247#<br />
                                02. Choose 'Payment'<br />
                                03. Enter the business wallet number 01814812233<br />
                                04. Enter the amount you want to pay<br />
                                05. Enter a reference against your payment<br />
                                06. Now enter your PIN to confirm<br />
                                07. Done! You will get a confirmation SMS
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Nagad (Merchant)</h5>
                            <p class="card-text">
                                Nagad Details:<br />
                                A/C- 01841539963 (Merchant)<br />
                                *How to make payment?<br />
                                01. Go to Nogad Menu by dialing *167#<br />
                                02. Choose 'Payment'<br />
                                03. Enter the business wallet number<br />
                                04. Enter the amount you want to Pay<br />
                                05. Enter a reference against your payment<br />
                                06. Now enter your PIN to confirm<br />
                                07. Done! You will get a confirmation SMS
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card ">
                        <a href="{{url('dropshipper/dashboard')}}" class="btn btn-primary">Back</a>
                        <form action="{{url('/dropshipper/store-deposit')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name_en">Amount</label>
                                    <input type="number" name="amount" class="form-control" id="amount" value="{{old('amount')}}" placeholder="Enter amount" required>
                                </div>
                                @error('amount')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="payment_gateway">Payment Gateway</label>
                                    <select name="payment_gateway" id="payment_gateway" class="form-control">
                                        <option value="" selected disabled>Select Payment Gateway</option>
                                        <option value="bkash(01814812233)">Bkash(01814812233)</option>
                                        <option value="nagad(01841539963)">Nagad(01841539963)</option>
                                    </select>
                                </div>
                                @error('payment_gateway')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="transaction_id">Transaction ID</label>
                                    <input type="text" name="transaction_id" class="form-control" id="transaction_id" value="{{old('transaction_id')}}" placeholder="Enter transaction_id" required>
                                </div>
                                @error('transaction_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Page Content -->
@endsection
