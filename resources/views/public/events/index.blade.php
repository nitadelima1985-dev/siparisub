@extends('layouts.public', ['title' => 'Event Wisata - SIPARISUB'])

@section('content')
    <section class="page-hero py-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-light text-success mb-3">Agenda Terpublikasi</span>
                    <h1 class="display-5 fw-bold mb-3">Event Wisata Kabupaten Subang</h1>
                    <p class="lead text-white-50 mb-0">
                        Temukan festival, agenda budaya, dan kegiatan wisata yang sudah dipublikasikan di SIPARISUB.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="filter-box">
                        <form method="GET" action="{{ route('public.events.index') }}">
                            <label class="form-label text-dark" for="period">Filter Event</label>
                            <select class="form-select mb-3" id="period" name="period">
                                <option value="">Semua event</option>
                                <option value="upcoming" @selected($period === 'upcoming')>Upcoming</option>
                                <option value="past" @selected($period === 'past')>Past</option>
                            </select>
                            <div class="d-grid gap-2">
                                <button class="btn btn-success" type="submit">
                                    <i class="fa-solid fa-filter me-1"></i>Filter
                                </button>
                                <a class="btn btn-outline-secondary" href="{{ route('public.events.index') }}">Reset</a>
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
                    <span class="text-success fw-bold">AGENDA WISATA</span>
                    <h2 class="section-title mt-2 mb-0">{{ $events->total() }} event ditemukan</h2>
                </div>
            </div>

            <div class="row g-4">
                @forelse($events as $event)
                    <div class="col-md-6 col-xl-4">
                        <div class="destination-card">
                            @if($event->cover_image)
                                <img class="destination-img" src="{{ asset('storage/'.$event->cover_image) }}" alt="{{ $event->title }}">
                            @else
                                <div class="destination-img d-flex align-items-center justify-content-center text-secondary">
                                    <i class="fa-solid fa-calendar-days fs-1"></i>
                                </div>
                            @endif
                            <div class="p-4">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge text-bg-success">{{ $event->start_date->translatedFormat('d M Y') }}</span>
                                    @if($event->destination)
                                        <span class="badge text-bg-light text-dark">{{ $event->destination->name }}</span>
                                    @endif
                                </div>
                                <h3 class="h5 fw-bold">{{ $event->title }}</h3>
                                <p class="text-muted">{{ \Illuminate\Support\Str::limit($event->short_description, 135) }}</p>
                                <div class="d-grid gap-2 small text-secondary mb-3">
                                    <div><i class="fa-solid fa-location-dot me-1"></i>{{ $event->location_name ?: $event->destination?->name ?: 'Lokasi belum tersedia' }}</div>
                                    <div><i class="fa-solid fa-building-user me-1"></i>{{ $event->organizer_name ?: 'Penyelenggara belum tersedia' }}</div>
                                </div>
                                <a class="btn btn-sm btn-outline-success rounded-pill px-3" href="{{ route('public.events.show', $event->slug) }}">
                                    Detail Event
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="info-card p-5 text-center">
                            <i class="fa-solid fa-calendar-xmark fs-1 text-success mb-3"></i>
                            <h2 class="h4 fw-bold">Event belum tersedia</h2>
                            <p class="text-muted mb-0">Belum ada event published yang sesuai dengan filter saat ini.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $events->links() }}
            </div>
        </div>
    </section>
@endsection
