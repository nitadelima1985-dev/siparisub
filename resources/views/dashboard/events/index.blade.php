@extends('layouts.dashboard', ['title' => 'Event Wisata'])

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">Event Wisata</h1>
            <p class="text-secondary mb-0">Kelola agenda wisata dan proses approval event.</p>
        </div>
        @can('create', App\Models\Event::class)
            <a class="btn btn-primary" href="{{ route('dashboard.events.create') }}">Tambah Event</a>
        @endcan
    </div>

    @if(auth()->user()->hasRole('super_admin', 'admin_dinas', 'reviewer_akademik'))
        <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
            <span class="small text-secondary fw-semibold">Antrean Review:</span>
            @foreach($reviewQueueStatuses as $queueStatus)
                <a class="btn btn-sm {{ request('status') === $queueStatus->value ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('dashboard.events.index', ['status' => $queueStatus->value]) }}">
                    {{ $queueStatus->label() }}
                </a>
            @endforeach
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard.events.index') }}">Semua</a>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET" action="{{ route('dashboard.events.index') }}">
                <div class="col-md-3">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="destination">Destinasi</label>
                    <select class="form-select" id="destination" name="destination">
                        <option value="">Semua destinasi</option>
                        @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" @selected((string) request('destination') === (string) $destination->id)>{{ $destination->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="date_from">Mulai Dari</label>
                    <input class="form-control" id="date_from" name="date_from" type="date" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="date_to">Sampai</label>
                    <input class="form-control" id="date_to" name="date_to" type="date" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-outline-primary w-100" type="submit">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Event</th>
                        <th>Destinasi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Pembuat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $event->title }}</div>
                                <div class="small text-secondary">{{ $event->location_name ?: '-' }}</div>
                            </td>
                            <td>{{ $event->destination?->name ?: '-' }}</td>
                            <td>
                                {{ $event->start_date?->format('d M Y') }}
                                @if($event->end_date) - {{ $event->end_date->format('d M Y') }} @endif
                            </td>
                            <td><span class="badge text-bg-secondary">{{ $event->workflow_status->label() }}</span></td>
                            <td>{{ $event->creator?->name }}</td>
                            <td class="text-end">
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('dashboard.events.show', $event) }}">Detail</a>
                                @can('update', $event)
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('dashboard.events.edit', $event) }}">Edit</a>
                                @endcan
                                @can('delete', $event)
                                    <form class="d-inline" method="POST" action="{{ route('dashboard.events.destroy', $event) }}" onsubmit="return confirm('Yakin ingin menghapus event ini? Data approval dan file cover terkait juga akan dihapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Belum ada event.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">
            {{ $events->links() }}
        </div>
    </div>
@endsection
