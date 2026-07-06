@extends('layouts.public', ['title' => $event->title.' - SIPARISUB'])

@section('content')
    <section class="page-hero py-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge text-bg-light text-success">{{ $event->start_date->translatedFormat('d F Y') }}</span>
                        @if($event->destination)
                            <span class="badge text-bg-light text-dark">{{ $event->destination->name }}</span>
                        @endif
                    </div>
                    <h1 class="display-5 fw-bold mb-3">{{ $event->title }}</h1>
                    <p class="lead text-white-50 mb-4">{{ $event->short_description }}</p>
                    <a class="btn btn-outline-light rounded-pill px-4" href="{{ route('public.events.index') }}">
                        <i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Event
                    </a>
                </div>
                <div class="col-lg-5">
                    @if($event->cover_image)
                        <img class="w-100 rounded-4 shadow" style="height: 340px; object-fit: cover;" src="{{ asset('storage/'.$event->cover_image) }}" alt="{{ $event->title }}">
                    @else
                        <div class="rounded-4 bg-light text-secondary d-flex align-items-center justify-content-center" style="height: 340px;">
                            <i class="fa-solid fa-calendar-days fs-1"></i>
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
                    <div class="info-card p-4 mb-4">
                        <h2 class="h4 fw-bold mb-3">Tentang Event</h2>
                        <div class="text-muted" style="white-space: pre-wrap;">{{ $event->full_description ?: $event->short_description }}</div>
                    </div>

                    @if($event->destination)
                        <div class="info-card p-4">
                            <h2 class="h4 fw-bold mb-3">Destinasi Terkait</h2>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <div class="fw-bold">{{ $event->destination->name }}</div>
                                    <div class="text-secondary small">{{ $event->destination->category?->name }} - {{ $event->destination->district?->name }}</div>
                                </div>
                                <a class="btn btn-outline-success rounded-pill px-4" href="{{ route('public.destinations.show', $event->destination->slug) }}">Lihat Destinasi</a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="info-card p-4 mb-4">
                        <h2 class="h5 fw-bold mb-3">Informasi Event</h2>
                        <div class="d-grid gap-3">
                            <div>
                                <div class="text-secondary small">Tanggal</div>
                                <div class="fw-semibold">
                                    {{ $event->start_date->translatedFormat('d F Y') }}
                                    @if($event->end_date)
                                        - {{ $event->end_date->translatedFormat('d F Y') }}
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-secondary small">Waktu</div>
                                <div class="fw-semibold">{{ $event->start_time ? \Illuminate\Support\Str::of($event->start_time)->substr(0, 5) : '-' }} @if($event->end_time)- {{ \Illuminate\Support\Str::of($event->end_time)->substr(0, 5) }}@endif</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Lokasi</div>
                                <div class="fw-semibold">{{ $event->location_name ?: $event->destination?->name ?: '-' }}</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Penyelenggara</div>
                                <div class="fw-semibold">{{ $event->organizer_name ?: '-' }}</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Kontak</div>
                                <div class="fw-semibold">{{ $event->contact_phone ?: '-' }}</div>
                            </div>
                            <div class="small text-secondary">
                                Dipublikasikan: {{ optional($event->published_at)->translatedFormat('d F Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
