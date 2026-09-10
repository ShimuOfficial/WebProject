@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" data-auto-hide="5000">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" data-auto-hide="5000">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert" data-auto-hide="5000">
        <i class="fas fa-triangle-exclamation me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger fade show" role="alert"
        data-auto-hide="{{ config('order_messages.ui.alert_autohide_ms', 3000) }}">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const MAX_MS = 5000;
        document.querySelectorAll('.alert[data-auto-hide]').forEach(function(el) {
            const attr = el.getAttribute('data-auto-hide');
            const ms = parseInt(attr, 10) || MAX_MS;
            const timeout = Math.min(ms, MAX_MS);
            setTimeout(function() {
                try {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                        // Use Bootstrap's dismiss if available
                        const a = new bootstrap.Alert(el);
                        a.close();
                        return;
                    }
                } catch (e) {
                    // ignore
                }
                // Fallback: fade out then remove
                el.classList.remove('show');
                setTimeout(function() {
                    el.remove();
                }, 300);
            }, timeout);
        });
    });
</script>
