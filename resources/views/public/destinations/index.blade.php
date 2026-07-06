@extends('layouts.public', ['title' => 'Destinasi Wisata - SIPARISUB'])

@section('content')
    <section class="page-hero py-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-light text-success mb-3">Destinasi Terverifikasi</span>
                    <h1 class="display-5 fw-bold mb-3">Jelajahi Destinasi Wisata Kabupaten Subang</h1>
                    <p class="lead text-white-50 mb-0">
                        Temukan destinasi yang sudah dipublikasikan melalui proses validasi SIPARISUB.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="filter-box">
                        <form method="GET" action="{{ route('public.destinations.index') }}">
                            <div class="mb-3">
                                <label class="form-label text-dark" for="q">Cari Destinasi</label>
                                <input class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Nama destinasi...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark" for="category">Kategori</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">Semua kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark" for="district">Kecamatan</label>
                                <select class="form-select" id="district" name="district">
                                    <option value="">Semua kecamatan</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" @selected((string) request('district') === (string) $district->id)>{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-success" type="submit">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i>Filter
                                </button>
                                <a class="btn btn-outline-secondary" href="{{ route('public.destinations.index') }}">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            @php
                $activeCategory = request('category') ? $categories->firstWhere('id', (int) request('category')) : null;
                $activeDistrict = request('district') ? $districts->firstWhere('id', (int) request('district')) : null;
                $hasActiveFilter = request()->filled('q') || request()->filled('category') || request()->filled('district');
            @endphp

            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
                <div>
                    <span class="text-success fw-bold">DIREKTORI DESTINASI</span>
                    <h2 class="section-title mt-2 mb-0">{{ $destinations->total() }} destinasi ditemukan</h2>
                </div>

                @if($hasActiveFilter)
                    <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('public.destinations.index') }}">
                        <i class="fa-solid fa-rotate-left me-1"></i>Reset Filter
                    </a>
                @endif
            </div>

            @if($hasActiveFilter)
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="badge text-bg-light text-dark border">Filter aktif</span>
                    @if(request()->filled('q'))
                        <span class="badge text-bg-success">Kata kunci: {{ request('q') }}</span>
                    @endif
                    @if($activeCategory)
                        <span class="badge text-bg-success">Kategori: {{ $activeCategory->name }}</span>
                    @endif
                    @if($activeDistrict)
                        <span class="badge text-bg-success">Kecamatan: {{ $activeDistrict->name }}</span>
                    @endif
                </div>
            @endif

            <div class="row g-4">
                @forelse($destinations as $destination)
                    <div class="col-md-6 col-xl-4">
                        <div class="destination-card">
                            @php($coverUrl = $destination->coverMedia?->file_path ? asset('storage/'.$destination->coverMedia->file_path) : $destination->coverMedia?->external_url)
                            @if($coverUrl)
                                <img class="destination-img" src="{{ $coverUrl }}" alt="{{ $destination->name }}">
                            @else
                                <div class="destination-img d-flex align-items-center justify-content-center text-secondary">
                                    <i class="fa-solid fa-image fs-1"></i>
                                </div>
                            @endif
                            <div class="p-4">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge text-bg-success">{{ $destination->category?->name }}</span>
                                    <span class="badge text-bg-light text-dark">{{ $destination->district?->name }}</span>
                                </div>
                                <h3 class="h5 fw-bold">{{ $destination->name }}</h3>
                                <p class="text-muted">{{ \Illuminate\Support\Str::limit($destination->short_description, 135) }}</p>
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <span class="small text-secondary">
                                        <i class="fa-solid fa-location-dot me-1"></i>{{ $destination->village_name ?: $destination->district?->name }}
                                    </span>
                                    <a class="btn btn-sm btn-outline-success rounded-pill px-3" href="{{ route('public.destinations.show', $destination->slug) }}">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="info-card p-5 text-center">
                            <i class="fa-solid fa-map-location-dot fs-1 text-success mb-3"></i>
                            <h2 class="h4 fw-bold">Destinasi belum ditemukan</h2>
                            <p class="text-muted mb-3">Coba ubah kata kunci, kategori, atau kecamatan yang dipilih.</p>
                            <a class="btn btn-outline-success rounded-pill px-4" href="{{ route('public.destinations.index') }}">Reset Filter</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $destinations->links() }}
            </div>
        </div>
    </section>
@endsection


