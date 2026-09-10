@extends('layouts.app')
@section('title', 'Notifications')
@section('subtitle', 'Operational alerts and updates')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="text-xs muted">Important alerts</div>
            <div class="fw-bold text-lg">{{ $notificationCount }} active
                notification{{ $notificationCount === 1 ? '' : 's' }}</div>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Dashboard</a>
    </div>

    <div class="row g-4">
        @foreach ($notifications as $note)
            <div class="col-xl-6 fade-in">
                <div class="card h-100 {{ !empty($note['is_new']) ? 'border border-danger' : '' }}">
                    <div class="card-body d-flex gap-3 align-items-start">
                        <div class="icon-42"
                            style="background:{{ $note['type'] === 'danger' ? 'rgba(239,68,68,.12)' : ($note['type'] === 'warning' ? 'rgba(245,158,11,.12)' : ($note['type'] === 'info' ? 'rgba(6,182,212,.12)' : 'rgba(16,185,129,.12)')) }};color:{{ $note['type'] === 'danger' ? '#ef4444' : ($note['type'] === 'warning' ? '#f59e0b' : ($note['type'] === 'info' ? '#06b6d4' : '#10b981')) }};">
                            <i
                                class="fas {{ $note['type'] === 'danger' ? 'fa-triangle-exclamation' : ($note['type'] === 'warning' ? 'fa-clock' : ($note['type'] === 'info' ? 'fa-bell' : 'fa-circle-check')) }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            @if (!empty($note['is_new']))
                                <div class="badge bg-danger text-white mb-2">New</div>
                            @endif
                            <div class="fw-bold mb-1">{{ $note['title'] }}</div>
                            <div class="text-base muted">{{ $note['message'] }}</div>
                            <a href="{{ $note['link'] }}"
                                class="btn btn-sm btn-outline-secondary mt-3">{{ $note['link_text'] }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
