@extends('layouts.dashboard', ['title' => 'Artikel'])

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">Artikel</h1>
            <p class="text-secondary mb-0">Kelola artikel promosi dan proses approval konten.</p>
        </div>
        @can('create', App\Models\Article::class)
            <a class="btn btn-primary" href="{{ route('dashboard.articles.create') }}">Tambah Artikel</a>
        @endcan
    </div>

    @if(auth()->user()->hasRole('super_admin', 'admin_dinas', 'reviewer_akademik'))
        <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
            <span class="small text-secondary fw-semibold">Antrean Review:</span>
            @foreach($reviewQueueStatuses as $queueStatus)
                <a class="btn btn-sm {{ request('status') === $queueStatus->value ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('dashboard.articles.index', ['status' => $queueStatus->value]) }}">
                    {{ $queueStatus->label() }}
                </a>
            @endforeach
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard.articles.index') }}">Semua</a>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET" action="{{ route('dashboard.articles.index') }}">
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
                    <label class="form-label" for="category">Kategori</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Semua kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="destination">Destinasi</label>
                    <select class="form-select" id="destination" name="destination">
                        <option value="">Semua destinasi</option>
                        @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" @selected((string) request('destination') === (string) $destination->id)>{{ $destination->name }}</option>
                        @endforeach
                    </select>
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
                        <th>Artikel</th>
                        <th>Kategori</th>
                        <th>Destinasi</th>
                        <th>Status</th>
                        <th>Pembuat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $article->title }}</div>
                                <div class="small text-secondary">{{ $article->excerpt ?: '-' }}</div>
                            </td>
                            <td>{{ $article->category?->name ?: '-' }}</td>
                            <td>{{ $article->destination?->name ?: '-' }}</td>
                            <td><span class="badge text-bg-secondary">{{ $article->workflow_status->label() }}</span></td>
                            <td>{{ $article->creator?->name }}</td>
                            <td class="text-end">
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('dashboard.articles.show', $article) }}">Detail</a>
                                @can('update', $article)
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('dashboard.articles.edit', $article) }}">Edit</a>
                                @endcan
                                @can('delete', $article)
                                    <form class="d-inline" method="POST" action="{{ route('dashboard.articles.destroy', $article) }}" onsubmit="return confirm('Yakin ingin menghapus artikel ini? Data approval dan file gambar terkait juga akan dihapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Belum ada artikel.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">
            {{ $articles->links() }}
        </div>
    </div>
@endsection
