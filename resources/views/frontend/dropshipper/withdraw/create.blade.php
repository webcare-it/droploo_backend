@extends('frontend.dropshipper.master')

@section('content')
    <!-- Page header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Make Withdraw Request</h4>
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
                    <div class="card ">
                        <a href="{{url('dropshipper/withdraw-history')}}" class="btn btn-primary">Back</a>
                        <form action="{{url('/dropshipper/store-withdraw')}}" method="POST" enctype="multipart/form-data">
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
                                    <label for="transaction_id">Notes (Optional)</label>
                                   <textarea name="notes" id="notes" class="form-control"></textarea>
                                </div>
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
