@extends('layouts.public', ['title' => 'Artikel Wisata - SIPARISUB'])

@section('content')
    <section class="page-hero py-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-light text-success mb-3">Konten Terpublikasi</span>
                    <h1 class="display-5 fw-bold mb-3">Artikel dan Konten Promosi Wisata</h1>
                    <p class="lead text-white-50 mb-0">
                        Baca cerita, panduan, dan informasi promosi wisata Subang yang sudah melalui proses publikasi.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="filter-box">
                        <form method="GET" action="{{ route('public.articles.index') }}">
                            <label class="form-label text-dark" for="category">Kategori Artikel</label>
                            <select class="form-select mb-3" id="category" name="category">
                                <option value="">Semua kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="d-grid gap-2">
                                <button class="btn btn-success" type="submit">
                                    <i class="fa-solid fa-filter me-1"></i>Filter
                                </button>
                                <a class="btn btn-outline-secondary" href="{{ route('public.articles.index') }}">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
                <div>
                    <span class="text-success fw-bold">KONTEN WISATA</span>
                    <h2 class="section-title mt-2 mb-0">{{ $articles->total() }} artikel ditemukan</h2>
                </div>
            </div>

            <div class="row g-4">
                @forelse($articles as $article)
                    <div class="col-md-6 col-xl-4">
                        <div class="destination-card">
                            @if($article->featured_image)
                                <img class="destination-img" src="{{ asset('storage/'.$article->featured_image) }}" alt="{{ $article->title }}">
                            @else
                                <div class="destination-img d-flex align-items-center justify-content-center text-secondary">
                                    <i class="fa-solid fa-newspaper fs-1"></i>
                                </div>
                            @endif
                            <div class="p-4">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge text-bg-success">{{ $article->category?->name }}</span>
                                    @if($article->destination)
                                        <span class="badge text-bg-light text-dark">{{ $article->destination->name }}</span>
                                    @endif
                                </div>
                                <h3 class="h5 fw-bold">{{ $article->title }}</h3>
                                <p class="text-muted">{{ \Illuminate\Support\Str::limit($article->excerpt ?: strip_tags($article->content), 135) }}</p>
                                <div class="d-grid gap-2 small text-secondary mb-3">
                                    <div><i class="fa-solid fa-user-pen me-1"></i>{{ $article->creator?->name ?: 'SIPARISUB' }}</div>
                                    <div><i class="fa-solid fa-calendar-check me-1"></i>{{ optional($article->published_at)->translatedFormat('d M Y') }}</div>
                                </div>
                                <a class="btn btn-sm btn-outline-success rounded-pill px-3" href="{{ route('public.articles.show', $article->slug) }}">
                                    Baca Artikel
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="info-card p-5 text-center">
                            <i class="fa-solid fa-newspaper fs-1 text-success mb-3"></i>
                            <h2 class="h4 fw-bold">Artikel belum tersedia</h2>
                            <p class="text-muted mb-0">Belum ada artikel published yang sesuai dengan kategori saat ini.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $articles->links() }}
            </div>
        </div>
    </section>
@endsection
