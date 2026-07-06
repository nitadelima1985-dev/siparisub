@extends('layouts.dashboard', ['title' => 'Detail Destinasi'])

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">{{ $destination->name }}</h1>
            <p class="text-secondary mb-0">
                {{ $destination->category?->name }} di {{ $destination->district?->name }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('dashboard.destinations.index') }}">Kembali</a>
            @can('update', $destination)
                <a class="btn btn-primary" href="{{ route('dashboard.destinations.edit', $destination) }}">Edit</a>
            @endcan
            @can('delete', $destination)
                <form method="POST" action="{{ route('dashboard.destinations.destroy', $destination) }}" onsubmit="return confirm('Yakin ingin menghapus destinasi ini? Data media dan riwayat approval terkait juga akan dihapus.')">
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
                @if($destination->coverMedia?->file_path)
                    <img class="card-img-top object-fit-cover" style="max-height: 360px;" src="{{ asset('storage/'.$destination->coverMedia->file_path) }}" alt="{{ $destination->name }}">
                @endif
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge text-bg-secondary">{{ $destination->workflow_status->label() }}</span>
                        @if($destination->is_featured)
                            <span class="badge text-bg-warning">Unggulan</span>
                        @endif
                        <span class="badge {{ $destination->is_active ? 'text-bg-success' : 'text-bg-light' }}">
                            {{ $destination->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    @if($destination->latestApproval?->latest_note)
                        <div class="alert alert-warning">
                            <div class="fw-semibold mb-1">Catatan review terakhir</div>
                            <div>{{ $destination->latestApproval->latest_note }}</div>
                        </div>
                    @endif

                    <h2 class="h5">Ringkasan</h2>
                    <p>{{ $destination->short_description }}</p>

                    @if($destination->full_description)
                        <h2 class="h5 mt-4">Deskripsi</h2>
                        <p class="mb-0">{{ $destination->full_description }}</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Informasi Lokasi dan Operasional</h2>
                    <div class="row g-3">
                        <div class="col-md-6"><div class="text-secondary small">Alamat</div><div>{{ $destination->address }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Desa/Kelurahan</div><div>{{ $destination->village_name ?: '-' }}</div></div>
                        <div class="col-md-3"><div class="text-secondary small">Latitude</div><div>{{ $destination->latitude ?: '-' }}</div></div>
                        <div class="col-md-3"><div class="text-secondary small">Longitude</div><div>{{ $destination->longitude ?: '-' }}</div></div>
                        <div class="col-md-3"><div class="text-secondary small">Hari Buka</div><div>{{ $destination->open_days ?: '-' }}</div></div>
                        <div class="col-md-3"><div class="text-secondary small">Jam Buka</div><div>{{ $destination->open_hours ?: '-' }}</div></div>
                        <div class="col-md-4"><div class="text-secondary small">Tiket Dewasa</div><div>{{ $destination->ticket_adult ?: '-' }}</div></div>
                        <div class="col-md-4"><div class="text-secondary small">Tiket Anak</div><div>{{ $destination->ticket_child ?: '-' }}</div></div>
                        <div class="col-md-4"><div class="text-secondary small">Parkir</div><div>{{ $destination->parking_fee ?: '-' }}</div></div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Daya Tarik dan Akses</h2>
                    <div class="row g-3">
                        <div class="col-md-6"><div class="text-secondary small">Daya Tarik</div><div>{{ $destination->main_attraction ?: '-' }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Aktivitas</div><div>{{ $destination->activities ?: '-' }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Waktu Terbaik</div><div>{{ $destination->best_visit_time ?: '-' }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Catatan Akses</div><div>{{ $destination->access_notes ?: '-' }}</div></div>
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
                        <div class="fw-semibold">{{ $destination->workflow_status->label() }}</div>
                    </div>

                    @can('submit', $destination)
                        <form method="POST" action="{{ route('dashboard.destinations.submit', $destination) }}" class="mb-3">
                            @csrf
                            <button class="btn btn-primary w-100" type="submit">Submit for Review</button>
                        </form>
                    @endcan

                    @can('review', $destination)
                        <form method="POST" action="{{ route('dashboard.destinations.review.under-review', $destination) }}" class="mb-3">
                            @csrf
                            <label class="form-label" for="under_review_note">Catatan Reviewer</label>
                            <textarea class="form-control mb-2" id="under_review_note" name="note" rows="2"></textarea>
                            <button class="btn btn-outline-primary w-100" type="submit">Tandai Under Review</button>
                        </form>

                        <form method="POST" action="{{ route('dashboard.destinations.review.revision-needed', $destination) }}" class="mb-3">
                            @csrf
                            <label class="form-label" for="revision_note">Catatan Revisi</label>
                            <textarea class="form-control mb-2" id="revision_note" name="note" rows="3" required></textarea>
                            <button class="btn btn-warning w-100" type="submit">Minta Revisi</button>
                        </form>
                    @endcan

                    @can('approve', $destination)
                        <form method="POST" action="{{ route('dashboard.destinations.approve', $destination) }}" class="mb-3">
                            @csrf
                            <label class="form-label" for="approve_note">Catatan Approval</label>
                            <textarea class="form-control mb-2" id="approve_note" name="note" rows="2"></textarea>
                            <button class="btn btn-success w-100" type="submit">Approve Destinasi</button>
                        </form>
                    @endcan

                    @can('publish', $destination)
                        <form method="POST" action="{{ route('dashboard.destinations.publish', $destination) }}" class="mb-3">
                            @csrf
                            <button class="btn btn-primary w-100" type="submit">Publish Destinasi</button>
                        </form>
                    @endcan

                    @can('archive', $destination)
                        <form method="POST" action="{{ route('dashboard.destinations.archive', $destination) }}">
                            @csrf
                            <button class="btn btn-outline-danger w-100" type="submit">Archive Destinasi</button>
                        </form>
                    @endcan

                    @can('restoreArchive', $destination)
                        <form method="POST" action="{{ route('dashboard.destinations.restore-archive', $destination) }}" class="mt-3">
                            @csrf
                            <label class="form-label" for="restore_note">Catatan Buka Arsip</label>
                            <textarea class="form-control mb-2" id="restore_note" name="note" rows="2" placeholder="Opsional"></textarea>
                            <button class="btn btn-success w-100" type="submit">Buka Kembali Arsip</button>
                        </form>
                    @endcan
                </div>
            </div>

            @can('update', $destination)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Upload Cover</h2>
                        <form method="POST" action="{{ route('dashboard.destinations.cover.store', $destination) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="cover_image">Gambar Cover</label>
                                <input class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" type="file" accept="image/*" required>
                                @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="caption">Caption</label>
                                <input class="form-control @error('caption') is-invalid @enderror" id="caption" name="caption" value="{{ old('caption') }}">
                                @error('caption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button class="btn btn-outline-primary w-100" type="submit">Upload Cover</button>
                        </form>
                    </div>
                </div>
            @endcan

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

