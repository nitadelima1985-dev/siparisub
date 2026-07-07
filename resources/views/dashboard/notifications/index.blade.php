@extends('layouts.dashboard', ['title' => 'Notifikasi'])

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">Notifikasi</h1>
            <p class="text-secondary mb-0">Pembaruan workflow dan pesan internal SIPARISUB.</p>
        </div>
        <form method="POST" action="{{ route('dashboard.notifications.read-all') }}">
            @csrf
            <button class="btn btn-outline-primary" type="submit">Tandai Semua Dibaca</button>
        </form>
    </div>

    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
            <div class="list-group-item px-0 py-3 {{ $notification->read_at ? '' : 'bg-light' }}">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="fw-semibold">{{ $notification->title }}</div>
                        <div class="text-secondary small mb-2">{{ $notification->message }}</div>
                        <div class="small text-muted">
                            {{ $notification->content_type ?: 'Workflow' }}
                            @if($notification->status) - {{ $notification->status }} @endif
                            - {{ $notification->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('dashboard.notifications.read', $notification) }}">
                        @csrf
                        <button class="btn btn-sm {{ $notification->read_at ? 'btn-outline-secondary' : 'btn-primary' }}" type="submit">
                            {{ $notification->action_url ? 'Buka' : 'Dibaca' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center text-secondary py-5">Belum ada notifikasi.</div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
@endsection