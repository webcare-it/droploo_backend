@extends('admin.master')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="card card-radius-10">
                <div class="card-header">
                    <h5 class="mb-1">Order Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.order.settings.update') }}" method="post">
                        @csrf
                        
                        <!-- Order Status Toggle -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">
                                        Order Creation Status
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="order_status" 
                                               name="order_status" 
                                               value="1" 
                                               {{ $settings->order_status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="order_status">
                                            <span id="status_text">{{ $settings->order_status ? 'Enabled' : 'Disabled' }}</span>
                                        </label>
                                    </div>
                                    <small class="text-muted">When disabled, API order creation will be blocked</small>
                                </div>
                            </div>
                        </div>

                        <!-- Disable Message -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="order_disable_message" style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">
                                        Order Disable Message
                                    </label>
                                    <textarea class="form-control" 
                                              id="order_disable_message" 
                                              name="order_disable_message" 
                                              rows="5" 
                                              placeholder="Enter message to show when order creation is disabled">{{ $settings->order_disable_message }}</textarea>
                                    <small class="text-muted">This message will be shown to API users when order creation is disabled</small>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save"></i> Save Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
<script>
    // Toggle status text
    document.getElementById('order_status').addEventListener('change', function() {
        document.getElementById('status_text').textContent = this.checked ? 'Enabled' : 'Disabled';
    });
</script>
@endpush
