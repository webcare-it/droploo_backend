@extends('layouts.admin')

@section('title', 'Order Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Creation Settings</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.order.settings.update') }}" method="POST">
                        @csrf

                        <!-- Order Status Toggle -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Order Creation Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" 
                                       id="order_status" name="order_status" value="1" 
                                       {{ $setting->order_status ? 'checked' : '' }}>
                                <label class="form-check-label" for="order_status">
                                    <span id="status_label" class="fw-bold {{ $setting->order_status ? 'text-success' : 'text-danger' }}">
                                        {{ $setting->order_status ? 'ACTIVE - Orders Can Be Created' : 'INACTIVE - Orders Cannot Be Created' }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Custom Message -->
                        <div class="mb-4" id="message_section" style="{{ $setting->order_status ? 'display:none;' : '' }}">
                            <label for="order_off_message" class="form-label fw-bold">
                                Message to Show When Orders Are Disabled
                            </label>
                            <textarea class="form-control" id="order_off_message" name="order_off_message" 
                                      rows="5" maxlength="1000" placeholder="Enter custom message to display when order creation is disabled...">{{ $setting->order_off_message }}</textarea>
                            <div class="form-text">This message will be shown to dropshippers when they try to create an order while the system is disabled.</div>
                        </div>

                        <!-- Preview -->
                        <div class="mb-4" id="preview_section" style="{{ $setting->order_status ? 'display:none;' : '' }}">
                            <label class="form-label fw-bold">Preview Message</label>
                            <div class="alert alert-warning">
                                <strong>API Response Example:</strong>
                                <pre id="message_preview" class="mb-0 mt-2" style="background: #f8f9fa; padding: 10px; border-radius: 5px;">{
  "status": "error",
  "message": "{{ $setting->order_off_message ?? 'Order creation is currently disabled. Please try again later.' }}"
}</pre>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ms-2">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusToggle = document.getElementById('order_status');
    const messageSection = document.getElementById('message_section');
    const previewSection = document.getElementById('preview_section');
    const statusLabel = document.getElementById('status_label');
    const messagePreview = document.getElementById('message_preview');
    const messageTextarea = document.getElementById('order_off_message');

    statusToggle.addEventListener('change', function() {
        if (this.checked) {
            // Active
            statusLabel.textContent = 'ACTIVE - Orders Can Be Created';
            statusLabel.className = 'fw-bold text-success';
            messageSection.style.display = 'none';
            previewSection.style.display = 'none';
        } else {
            // Inactive
            statusLabel.textContent = 'INACTIVE - Orders Cannot Be Created';
            statusLabel.className = 'fw-bold text-danger';
            messageSection.style.display = 'block';
            previewSection.style.display = 'block';
            updatePreview();
        }
    });

    messageTextarea.addEventListener('input', updatePreview);

    function updatePreview() {
        const message = messageTextarea.value || 'Order creation is currently disabled. Please try again later.';
        messagePreview.textContent = `{
  "status": "error",
  "message": "${message}"
}`;
    }
});
</script>
@endpush
@endsection
