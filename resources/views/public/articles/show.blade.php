@extends('layouts.public', ['title' => $article->title.' - SIPARISUB'])

@section('content')
    <section class="page-hero py-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge text-bg-light text-success">{{ $article->category?->name }}</span>
                        @if($article->destination)
                            <span class="badge text-bg-light text-dark">{{ $article->destination->name }}</span>
                        @endif
                    </div>
                    <h1 class="display-5 fw-bold mb-3">{{ $article->title }}</h1>
                    @if($article->excerpt)
                        <p class="lead text-white-50 mb-4">{{ $article->excerpt }}</p>
                    @endif
                    <div class="d-flex flex-wrap gap-3 text-white-50 mb-4">
                        <span><i class="fa-solid fa-user-pen me-1"></i>{{ $article->creator?->name ?: 'SIPARISUB' }}</span>
                        <span><i class="fa-solid fa-calendar-check me-1"></i>{{ optional($article->published_at)->translatedFormat('d F Y') }}</span>
                    </div>
                    <a class="btn btn-outline-light rounded-pill px-4" href="{{ route('public.articles.index') }}">
                        <i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Artikel
                    </a>
                </div>
                <div class="col-lg-5">
                    @if($article->featured_image)
                        <img class="w-100 rounded-4 shadow" style="height: 340px; object-fit: cover;" src="{{ asset('storage/'.$article->featured_image) }}" alt="{{ $article->title }}">
                    @else
                        <div class="rounded-4 bg-light text-secondary d-flex align-items-center justify-content-center" style="height: 340px;">
                            <i class="fa-solid fa-newspaper fs-1"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <article class="info-card p-4 mb-4">
                        <div class="text-muted" style="white-space: pre-wrap; line-height: 1.8;">{{ $article->content }}</div>
                    </article>

                    @if($article->source_name || $article->source_url)
                        <div class="info-card p-4">
                            <h2 class="h5 fw-bold mb-3">Sumber Informasi</h2>
                            <div class="text-secondary">{{ $article->source_name ?: 'Sumber eksternal' }}</div>
                            @if($article->source_url)
                                <a class="btn btn-outline-success rounded-pill px-4 mt-3" href="{{ $article->source_url }}" target="_blank" rel="noopener">
                                    Buka Sumber
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="info-card p-4 mb-4">
                        <h2 class="h5 fw-bold mb-3">Detail Artikel</h2>
                        <div class="d-grid gap-3">
                            <div>
                                <div class="text-secondary small">Kategori</div>
                                <div class="fw-semibold">{{ $article->category?->name ?: '-' }}</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Penulis</div>
                                <div class="fw-semibold">{{ $article->creator?->name ?: 'SIPARISUB' }}</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Tanggal Publish</div>
                                <div class="fw-semibold">{{ optional($article->published_at)->translatedFormat('d F Y') }}</div>
                            </div>
                        </div>
                    </div>

                    @if($article->destination)
                        <div class="info-card p-4">
                            <h2 class="h5 fw-bold mb-3">Destinasi Terkait</h2>
                            <div class="fw-bold">{{ $article->destination->name }}</div>
                            <p class="text-secondary small mb-3">{{ \Illuminate\Support\Str::limit($article->destination->short_description, 120) }}</p>
                            <a class="btn btn-outline-success rounded-pill px-4" href="{{ route('public.destinations.show', $article->destination->slug) }}">Lihat Destinasi</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
