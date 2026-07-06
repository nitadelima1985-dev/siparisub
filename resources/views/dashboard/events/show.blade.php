@extends('layouts.dashboard', ['title' => 'Detail Event'])

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">{{ $event->title }}</h1>
            <p class="text-secondary mb-0">{{ $event->destination?->name ?: 'Event umum' }} - {{ $event->location_name ?: 'Lokasi belum diisi' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('dashboard.events.index') }}">Kembali</a>
            @can('update', $event)
                <a class="btn btn-primary" href="{{ route('dashboard.events.edit', $event) }}">Edit</a>
            @endcan
            @can('delete', $event)
                <form method="POST" action="{{ route('dashboard.events.destroy', $event) }}" onsubmit="return confirm('Yakin ingin menghapus event ini? Data approval dan file cover terkait juga akan dihapus.')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit">Hapus</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                @if($event->cover_image)
                    <img class="card-img-top object-fit-cover" style="max-height: 360px;" src="{{ asset('storage/'.$event->cover_image) }}" alt="{{ $event->title }}">
                @endif
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge text-bg-secondary">{{ $event->workflow_status->label() }}</span>
                        <span class="badge {{ $event->is_active ? 'text-bg-success' : 'text-bg-light' }}">{{ $event->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>

                    @if($event->latestApproval?->latest_note)
                        <div class="alert alert-warning">
                            <div class="fw-semibold mb-1">Catatan review terakhir</div>
                            <div>{{ $event->latestApproval->latest_note }}</div>
                        </div>
                    @endif

                    <h2 class="h5">Ringkasan</h2>
                    <p>{{ $event->short_description }}</p>

                    @if($event->full_description)
                        <h2 class="h5 mt-4">Deskripsi</h2>
                        <p class="mb-0">{{ $event->full_description }}</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Informasi Event</h2>
                    <div class="row g-3">
                        <div class="col-md-6"><div class="text-secondary small">Penyelenggara</div><div>{{ $event->organizer_name ?: '-' }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Kontak</div><div>{{ $event->contact_phone ?: '-' }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Tanggal</div><div>{{ $event->start_date?->format('d M Y') }} @if($event->end_date) - {{ $event->end_date->format('d M Y') }} @endif</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Waktu</div><div>{{ $event->start_time ?: '-' }} @if($event->end_time) - {{ $event->end_time }} @endif</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Destinasi</div><div>{{ $event->destination?->name ?: '-' }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Pembuat</div><div>{{ $event->creator?->name ?: '-' }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Workflow Approval</h2>
                    <div class="mb-3">
                        <div class="text-secondary small">Status Saat Ini</div>
                        <div class="fw-semibold">{{ $event->workflow_status->label() }}</div>
                    </div>

                    @can('submit', $event)
                        <form method="POST" action="{{ route('dashboard.events.submit', $event) }}" class="mb-3">
                            @csrf
                            <button class="btn btn-primary w-100" type="submit">Submit for Review</button>
                        </form>
                    @endcan

                    @can('review', $event)
                        <form method="POST" action="{{ route('dashboard.events.review.under-review', $event) }}" class="mb-3">
                            @csrf
                            <label class="form-label" for="under_review_note">Catatan Reviewer</label>
                            <textarea class="form-control mb-2" id="under_review_note" name="note" rows="2"></textarea>
                            <button class="btn btn-outline-primary w-100" type="submit">Tandai Under Review</button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.events.review.revision-needed', $event) }}" class="mb-3">
                            @csrf
                            <label class="form-label" for="revision_note">Catatan Revisi</label>
                            <textarea class="form-control mb-2" id="revision_note" name="note" rows="3" required></textarea>
                            <button class="btn btn-warning w-100" type="submit">Minta Revisi</button>
                        </form>
                    @endcan

                    @can('approve', $event)
                        <form method="POST" action="{{ route('dashboard.events.approve', $event) }}" class="mb-3">
                            @csrf
                            <label class="form-label" for="approve_note">Catatan Approval</label>
                            <textarea class="form-control mb-2" id="approve_note" name="note" rows="2"></textarea>
                            <button class="btn btn-success w-100" type="submit">Approve Event</button>
                        </form>
                    @endcan

                    @can('publish', $event)
                        <form method="POST" action="{{ route('dashboard.events.publish', $event) }}" class="mb-3">
                            @csrf
                            <button class="btn btn-primary w-100" type="submit">Publish Event</button>
                        </form>
                    @endcan

                    @can('archive', $event)
                        <form method="POST" action="{{ route('dashboard.events.archive', $event) }}">
                            @csrf
                            <button class="btn btn-outline-danger w-100" type="submit">Archive Event</button>
                        </form>
                    @endcan

                    @can('restoreArchive', $event)
                        <form method="POST" action="{{ route('dashboard.events.restore-archive', $event) }}" class="mt-3">
                            @csrf
                            <label class="form-label" for="restore_note">Catatan Buka Arsip</label>
                            <textarea class="form-control mb-2" id="restore_note" name="note" rows="2" placeholder="Opsional"></textarea>
                            <button class="btn btn-success w-100" type="submit">Buka Kembali Arsip</button>
                        </form>
                    @endcan
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Riwayat Approval</h2>
                    @forelse($approvalLogs as $log)
                        <div class="border-bottom pb-2 mb-2">
                            <div class="fw-semibold">{{ $log->from_status?->label() ?? 'Awal' }} ke {{ $log->to_status->label() }}</div>
                            <div class="small text-secondary">{{ $log->actor?->name }} - {{ $log->created_at->format('d M Y H:i') }}</div>
                            @if($log->note)
                                <div class="small mt-1">{{ $log->note }}</div>
                            @endif
                        </div>
                    @empty
                        <p class="text-secondary mb-0">Belum ada riwayat approval.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
