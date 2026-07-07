@extends('layouts.dashboard', ['title' => 'Approval Konten'])

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">Approval Konten</h1>
            <p class="text-secondary mb-0">Antrean review, approval, publish, dan arsip untuk destinasi, event, serta artikel.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET" action="{{ route('dashboard.approvals.index') }}">
                <div class="col-md-5">
                    <label class="form-label" for="type">Jenis Konten</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Semua jenis</option>
                        @foreach($types as $typeValue => $typeLabel)
                            <option value="{{ $typeValue }}" @selected(request('type') === $typeValue)>{{ $typeLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button class="btn btn-outline-primary w-100" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('dashboard.approvals.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>Konten</th>
                    <th>Jenis</th>
                    <th>Status</th>
                    <th>Pengusul</th>
                    <th>Catatan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approvals as $approval)
                    @php
                        $content = $approval->approvable;
                        $contentName = $content?->name ?? $content?->title ?? 'Konten tidak ditemukan';
                        $contentType = match(true) {
                            $content instanceof \App\Models\Destination => 'Destinasi',
                            $content instanceof \App\Models\Event => 'Event',
                            $content instanceof \App\Models\Article => 'Artikel',
                            default => 'Konten',
                        };
                        $detailRoute = match(true) {
                            $content instanceof \App\Models\Destination => route('dashboard.destinations.show', $content),
                            $content instanceof \App\Models\Event => route('dashboard.events.show', $content),
                            $content instanceof \App\Models\Article => route('dashboard.articles.show', $content),
                            default => null,
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $contentName }}</div>
                            @if($detailRoute)
                                <a class="small text-decoration-none" href="{{ $detailRoute }}">Lihat detail konten</a>
                            @endif
                        </td>
                        <td>{{ $contentType }}</td>
                        <td><span class="badge text-bg-secondary">{{ $approval->current_status?->label() }}</span></td>
                        <td>{{ $approval->submitter?->name ?: '-' }}</td>
                        <td class="small text-secondary">{{ $approval->latest_note ?: '-' }}</td>
                        <td class="text-end">
                            @if($content)
                                <div class="d-flex flex-wrap justify-content-end gap-2">
                                    @can('review', $content)
                                        <form method="POST" action="{{ route('dashboard.approvals.under-review', $approval) }}">
                                            @csrf
                                            <button class="btn btn-outline-primary btn-sm" type="submit">Under Review</button>
                                        </form>
                                        <form method="POST" action="{{ route('dashboard.approvals.revision-needed', $approval) }}" class="d-flex gap-1">
                                            @csrf
                                            <input class="form-control form-control-sm" name="note" placeholder="Catatan revisi" required style="max-width: 180px;">
                                            <button class="btn btn-warning btn-sm" type="submit">Revisi</button>
                                        </form>
                                    @endcan
                                    @can('approve', $content)
                                        <form method="POST" action="{{ route('dashboard.approvals.approve', $approval) }}">
                                            @csrf
                                            <button class="btn btn-success btn-sm" type="submit">Approve</button>
                                        </form>
                                    @endcan
                                    @can('publish', $content)
                                        <form method="POST" action="{{ route('dashboard.approvals.publish', $approval) }}">
                                            @csrf
                                            <button class="btn btn-primary btn-sm" type="submit">Publish</button>
                                        </form>
                                    @endcan
                                    @can('archive', $content)
                                        <form method="POST" action="{{ route('dashboard.approvals.archive', $approval) }}" onsubmit="return confirm('Arsipkan konten ini?')">
                                            @csrf
                                            <button class="btn btn-outline-danger btn-sm" type="submit">Arsip</button>
                                        </form>
                                    @endcan
                                </div>
                            @else
                                <span class="text-secondary small">Konten tidak tersedia</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">Belum ada antrean approval.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $approvals->links() }}
@endsection