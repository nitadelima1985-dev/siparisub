@extends('layouts.public', ['title' => $destination->name.' - SIPARISUB'])

@section('content')
    <section class="page-hero py-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge text-bg-light text-success">{{ $destination->category?->name }}</span>
                        <span class="badge text-bg-light text-dark">{{ $destination->district?->name }}</span>
                    </div>
                    <h1 class="display-5 fw-bold mb-3">{{ $destination->name }}</h1>
                    <p class="lead text-white-50 mb-4">{{ $destination->short_description }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        @if($destination->google_maps_url)
                            <a class="btn btn-light rounded-pill px-4" href="{{ $destination->google_maps_url }}" target="_blank" rel="noopener">
                                <i class="fa-solid fa-map-location-dot me-1"></i>Buka Google Maps
                            </a>
                        @endif
                        <a class="btn btn-outline-light rounded-pill px-4" href="{{ route('public.destinations.index') }}">
                            <i class="fa-solid fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="col-lg-5">
                    @php($coverUrl = $destination->coverMedia?->file_path ? asset('storage/'.$destination->coverMedia->file_path) : $destination->coverMedia?->external_url)
                    @if($coverUrl)
                        <img class="w-100 rounded-4 shadow" style="height: 340px; object-fit: cover;" src="{{ $coverUrl }}" alt="{{ $destination->name }}">
                    @else
                        <div class="rounded-4 bg-light text-secondary d-flex align-items-center justify-content-center" style="height: 340px;">
                            <i class="fa-solid fa-image fs-1"></i>
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
                        <h2 class="h4 fw-bold mb-3">Tentang Destinasi</h2>
                        <div class="text-muted" style="white-space: pre-wrap;">{{ $destination->full_description ?: $destination->short_description }}</div>
                    </div>

                    <div class="info-card p-4 mb-4">
                        <h2 class="h4 fw-bold mb-3">Lokasi dan Akses</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-secondary small">Alamat</div>
                                <div class="fw-semibold">{{ $destination->address }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-secondary small">Desa/Kelurahan</div>
                                <div class="fw-semibold">{{ $destination->village_name ?: '-' }}</div>
                            </div>
                            @if($destination->access_notes)
                                <div class="col-12">
                                    <div class="text-secondary small">Catatan Akses</div>
                                    <div>{{ $destination->access_notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($destination->media->isNotEmpty())
                        <div class="info-card p-4">
                            <h2 class="h4 fw-bold mb-3">Galeri Foto</h2>
                            <div class="row g-3">
                                @foreach($destination->media as $media)
                                    @if($media->file_path)
                                        <div class="col-md-4">
                                            <img class="gallery-img" src="{{ asset('storage/'.$media->file_path) }}" alt="{{ $media->caption ?: $destination->name }}">
                                            @if($media->caption)
                                                <div class="small text-secondary mt-1">{{ $media->caption }}</div>
                                            @endif
                                        </div>
                                    @elseif($media->external_url)
                                        <div class="col-md-4">
                                            <img class="gallery-img" src="{{ $media->external_url }}" alt="{{ $media->caption ?: $destination->name }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="info-card p-4 mb-4">
                        <h2 class="h5 fw-bold mb-3">Informasi Kunjungan</h2>
                        <div class="d-grid gap-3">
                            <div>
                                <div class="text-secondary small">Hari Buka</div>
                                <div class="fw-semibold">{{ $destination->open_days ?: '-' }}</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Jam Operasional</div>
                                <div class="fw-semibold">{{ $destination->open_hours ?: '-' }}</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Tiket Dewasa</div>
                                <div class="fw-semibold">{{ $destination->ticket_adult !== null ? 'Rp '.number_format($destination->ticket_adult, 0, ',', '.') : '-' }}</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Tiket Anak</div>
                                <div class="fw-semibold">{{ $destination->ticket_child !== null ? 'Rp '.number_format($destination->ticket_child, 0, ',', '.') : '-' }}</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Parkir</div>
                                <div class="fw-semibold">{{ $destination->parking_fee !== null ? 'Rp '.number_format($destination->parking_fee, 0, ',', '.') : '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card p-4 mb-4">
                        <h2 class="h5 fw-bold mb-3">Daya Tarik</h2>
                        <div class="d-grid gap-3">
                            <div>
                                <div class="text-secondary small">Daya Tarik Utama</div>
                                <div>{{ $destination->main_attraction ?: '-' }}</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Aktivitas</div>
                                <div>{{ $destination->activities ?: '-' }}</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Waktu Terbaik</div>
                                <div>{{ $destination->best_visit_time ?: '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card p-4">
                        <h2 class="h5 fw-bold mb-3">Kontak dan Update</h2>
                        <div class="d-grid gap-3">
                            <div>
                                <div class="text-secondary small">Telepon</div>
                                <div>{{ $destination->contact_phone ?: '-' }}</div>
                            </div>
                            <div>
                                <div class="text-secondary small">Email</div>
                                <div>{{ $destination->contact_email ?: '-' }}</div>
                            </div>
                            @if($destination->website_url)
                                <a class="btn btn-outline-success rounded-pill" href="{{ $destination->website_url }}" target="_blank" rel="noopener">Website</a>
                            @endif
                            @if($destination->instagram_url)
                                <a class="btn btn-outline-success rounded-pill" href="{{ $destination->instagram_url }}" target="_blank" rel="noopener">Instagram</a>
                            @endif
                            @if($destination->tiktok_url)
                                <a class="btn btn-outline-success rounded-pill" href="{{ $destination->tiktok_url }}" target="_blank" rel="noopener">TikTok</a>
                            @endif
                            <div class="small text-secondary">
                                Terakhir diperbarui: {{ optional($destination->last_content_update_at ?? $destination->updated_at)->translatedFormat('d F Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
