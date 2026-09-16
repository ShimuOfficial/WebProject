<div class="policy-box">
    <h3>{{ $site['content']['refund_title'] ?? 'Cancellation & refund policy' }}</h3>
    <ul>
        @foreach ($site['content']['refund_items'] ?? [] as $item)
            <li><strong>{{ $item['title'] }}:</strong> {{ $item['text'] }}</li>
        @endforeach
    </ul>
</div>
