@extends('layouts.dashboard', ['title' => 'Detail Artikel'])

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">{{ $article->title }}</h1>
            <p class="text-secondary mb-0">{{ $article->category?->name }} @if($article->destination) - {{ $article->destination->name }} @endif</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('dashboard.articles.index') }}">Kembali</a>
            @can('update', $article)
                <a class="btn btn-primary" href="{{ route('dashboard.articles.edit', $article) }}">Edit</a>
            @endcan
            @can('delete', $article)
                <form method="POST" action="{{ route('dashboard.articles.destroy', $article) }}" onsubmit="return confirm('Yakin ingin menghapus artikel ini? Data approval dan file gambar terkait juga akan dihapus.')">
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
                @if($article->featured_image)
                    <img class="card-img-top object-fit-cover" style="max-height: 360px;" src="{{ asset('storage/'.$article->featured_image) }}" alt="{{ $article->title }}">
                @endif
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge text-bg-secondary">{{ $article->workflow_status->label() }}</span>
                        @if($article->is_featured)
                            <span class="badge text-bg-warning">Unggulan</span>
                        @endif
                        <span class="badge {{ $article->is_active ? 'text-bg-success' : 'text-bg-light' }}">{{ $article->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>

                    @if($article->latestApproval?->latest_note)
                        <div class="alert alert-warning">
                            <div class="fw-semibold mb-1">Catatan review terakhir</div>
                            <div>{{ $article->latestApproval->latest_note }}</div>
                        </div>
                    @endif

                    @if($article->excerpt)
                        <h2 class="h5">Excerpt</h2>
                        <p>{{ $article->excerpt }}</p>
                    @endif

                    <h2 class="h5 mt-4">Konten</h2>
                    <div style="white-space: pre-wrap;">{{ $article->content }}</div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Metadata</h2>
                    <div class="row g-3">
                        <div class="col-md-6"><div class="text-secondary small">Kategori</div><div>{{ $article->category?->name ?: '-' }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Destinasi</div><div>{{ $article->destination?->name ?: '-' }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Sumber</div><div>{{ $article->source_name ?: '-' }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">URL Sumber</div><div>{{ $article->source_url ?: '-' }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Pembuat</div><div>{{ $article->creator?->name ?: '-' }}</div></div>
                        <div class="col-md-6"><div class="text-secondary small">Approver</div><div>{{ $article->approver?->name ?: '-' }}</div></div>
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
                        <div class="fw-semibold">{{ $article->workflow_status->label() }}</div>
                    </div>

                    @can('submit', $article)
                        <form method="POST" action="{{ route('dashboard.articles.submit', $article) }}" class="mb-3">
                            @csrf
                            <button class="btn btn-primary w-100" type="submit">Submit for Review</button>
                        </form>
                    @endcan

                    @can('review', $article)
                        <form method="POST" action="{{ route('dashboard.articles.review.under-review', $article) }}" class="mb-3">
                            @csrf
                            <label class="form-label" for="under_review_note">Catatan Reviewer</label>
                            <textarea class="form-control mb-2" id="under_review_note" name="note" rows="2"></textarea>
                            <button class="btn btn-outline-primary w-100" type="submit">Tandai Under Review</button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.articles.review.revision-needed', $article) }}" class="mb-3">
                            @csrf
                            <label class="form-label" for="revision_note">Catatan Revisi</label>
                            <textarea class="form-control mb-2" id="revision_note" name="note" rows="3" required></textarea>
                            <button class="btn btn-warning w-100" type="submit">Minta Revisi</button>
                        </form>
                    @endcan

                    @can('approve', $article)
                        <form method="POST" action="{{ route('dashboard.articles.approve', $article) }}" class="mb-3">
                            @csrf
                            <label class="form-label" for="approve_note">Catatan Approval</label>
                            <textarea class="form-control mb-2" id="approve_note" name="note" rows="2"></textarea>
                            <button class="btn btn-success w-100" type="submit">Approve Artikel</button>
                        </form>
                    @endcan

                    @can('publish', $article)
                        <form method="POST" action="{{ route('dashboard.articles.publish', $article) }}" class="mb-3">
                            @csrf
                            <button class="btn btn-primary w-100" type="submit">Publish Artikel</button>
                        </form>
                    @endcan

                    @can('archive', $article)
                        <form method="POST" action="{{ route('dashboard.articles.archive', $article) }}">
                            @csrf
                            <button class="btn btn-outline-danger w-100" type="submit">Archive Artikel</button>
                        </form>
                    @endcan

                    @can('restoreArchive', $article)
                        <form method="POST" action="{{ route('dashboard.articles.restore-archive', $article) }}" class="mt-3">
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
