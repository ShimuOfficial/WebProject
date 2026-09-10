@extends('layouts.app')
@section('title', 'Kitchen Display')
@section('subtitle', 'Live ticket view for chefs')

@section('content')
    <div id="kitchenTickets">
        @include('admin.kitchen.partials.tickets', ['orders' => $orders])
    </div>

    @push('scripts')
        <script>
            const kitchenTicketsUrl = @json(route('kitchen.index', ['fragment' => 1]));
            const kitchenTicketsContainer = document.getElementById('kitchenTickets');
            let kitchenTimer = null;

            async function refreshKitchenTickets() {
                try {
                    const response = await fetch(kitchenTicketsUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) return;

                    kitchenTicketsContainer.innerHTML = await response.text();
                } catch (error) {
                    console.error('Failed to refresh kitchen tickets', error);
                }
            }

            function startKitchenPolling() {
                stopKitchenPolling();
                refreshKitchenTickets();
                kitchenTimer = setInterval(() => {
                    if (document.visibilityState === 'visible') {
                        refreshKitchenTickets();
                    }
                }, 15000);
            }

            function stopKitchenPolling() {
                if (kitchenTimer) {
                    clearInterval(kitchenTimer);
                    kitchenTimer = null;
                }
            }

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    refreshKitchenTickets();
                }
            });

            startKitchenPolling();
        </script>
    @endpush
@endsection
