@extends('layouts.public', ['title' => 'Peta Wisata - SIPARISUB'])

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQq2hRulfxgNktoJW93W8iL4xyw2K4M=" crossorigin="">

    <style>
        .map-shell {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            gap: 24px;
            align-items: stretch;
            min-width: 0;
        }

        .map-shell > * {
            min-width: 0;
        }

        #tourismMap {
            width: 100%;
            height: 620px;
            min-height: 620px;
            position: relative;
            border-radius: 22px;
            border: 1px solid #dbeafe;
            overflow: hidden;
            box-shadow: 0 14px 35px rgba(15, 23, 42, .08);
            background: #e2e8f0;
        }

        #tourismMap.leaflet-container {
            width: 100%;
            height: 620px;
            min-height: 620px;
            background: #dbeafe;
        }

        #tourismMap .leaflet-tile {
            max-width: none !important;
        }

        .map-list {
            max-height: 620px;
            overflow: auto;
        }

        .map-list-item {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #fff;
            padding: 14px;
            transition: .2s ease;
        }

        .map-list-item:hover {
            border-color: #0f766e;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .08);
        }

        .map-popup-img {
            width: 220px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 10px;
            background: #e2e8f0;
        }

        .leaflet-popup-content {
            min-width: 220px;
        }
        
                @media (max-width: 991.98px) {
            .map-shell {
                grid-template-columns: 1fr;
            }

            #tourismMap,
            #tourismMap.leaflet-container {
                height: 460px;
                min-height: 460px;
            }

            .map-list {
                max-height: none;
            }
        }
    </style>

    <section class="page-hero py-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-light text-success mb-3">Peta Wisata Interaktif</span>
                    <h1 class="display-5 fw-bold mb-3">Temukan Destinasi Wisata Subang di Peta</h1>
                    <p class="lead text-white-50 mb-0">
                        Jelajahi destinasi yang sudah terpublikasi dan memiliki koordinat lokasi resmi di SIPARISUB.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="filter-box">
                        <form method="GET" action="{{ route('public.map.index') }}">
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
                                    <i class="fa-solid fa-filter me-1"></i>Filter Peta
                                </button>
                                <a class="btn btn-outline-secondary" href="{{ route('public.map.index') }}">Reset Filter</a>
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
                    <span class="text-success fw-bold">SEBARAN DESTINASI</span>
                    <h2 class="section-title mt-2 mb-0">{{ $destinations->count() }} destinasi tampil di peta</h2>
                </div>
                <a class="btn btn-outline-success rounded-pill px-4" href="{{ route('public.destinations.index') }}">
                    <i class="fa-solid fa-list me-1"></i>Direktori Destinasi
                </a>
            </div>

            @if($destinations->isEmpty())
                <div class="info-card p-5 text-center">
                    <i class="fa-solid fa-map-location-dot fs-1 text-success mb-3"></i>
                    <h2 class="h4 fw-bold">Belum ada destinasi berkoordinat</h2>
                    <p class="text-muted mb-0">Belum ada destinasi published dan aktif yang memiliki latitude/longitude sesuai filter saat ini.</p>
                </div>
            @else
                <div class="map-shell">
                    <div id="tourismMap" aria-label="Peta wisata Kabupaten Subang"></div>

                    <aside class="map-list d-grid gap-3">
                        @foreach($destinations as $destination)
                            <a class="map-list-item text-decoration-none text-dark" href="{{ route('public.destinations.show', $destination->slug) }}" data-destination-id="{{ $destination->id }}">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="fw-bold">{{ $destination->name }}</div>
                                        <div class="small text-secondary">{{ $destination->district?->name ?: '-' }}</div>
                                    </div>
                                    <span class="badge text-bg-success">{{ $destination->category?->name ?: 'Destinasi' }}</span>
                                </div>
                                <div class="small text-muted mt-2">
                                    <i class="fa-solid fa-location-dot me-1"></i>{{ \Illuminate\Support\Str::limit($destination->address, 90) }}
                                </div>
                            </a>
                        @endforeach
                    </aside>
                </div>
            @endif
        </div>
    </section>

    @if($destinations->isNotEmpty())
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const destinations = @json($markers ?? []);
            const defaultCenter = [-6.5716, 107.7625];
            const map = L.map('tourismMap', {
                scrollWheelZoom: true,
            }).setView(defaultCenter, 10);

            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(map);

            const fallbackLayer = L.tileLayer('https://tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Tiles style by HOT',
            });

            let tileErrorCount = 0;
            osmLayer.on('tileerror', () => {
                tileErrorCount++;

                if (tileErrorCount >= 4 && !map.hasLayer(fallbackLayer)) {
                    map.removeLayer(osmLayer);
                    fallbackLayer.addTo(map);
                    refreshMapSize();
                }
            });

            const bounds = [];
            const markerById = new Map();

            destinations.forEach((destination) => {
                const lat = parseFloat(destination.latitude);
                const lng = parseFloat(destination.longitude);

                if (Number.isNaN(lat) || Number.isNaN(lng)) {
                      return;
                }

                const position = [lat, lng];;
                bounds.push(position);

                const image = destination.cover_url
                    ? `<img class="map-popup-img" src="${destination.cover_url}" alt="${escapeHtml(destination.name)}">`
                    : `<div class="map-popup-img d-flex align-items-center justify-content-center text-secondary"><i class="fa-solid fa-image fs-1"></i></div>`;

                const marker = L.marker(position)
                    .addTo(map)
                    .bindPopup(`
                        <div>
                            ${image}
                            <div class="fw-bold mb-1">${escapeHtml(destination.name)}</div>
                            <div class="small text-secondary mb-2">${escapeHtml(destination.category || 'Destinasi')} - ${escapeHtml(destination.district || '-')}</div>
                            <a class="btn btn-sm btn-success rounded-pill px-3" href="${destination.detail_url}">Lihat Detail</a>
                        </div>
                    `);

                markerById.set(String(destination.id), marker);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [34, 34], maxZoom: 14 });
            }
            
            setTimeout(() => {
                map.invalidateSize();

                if (bounds.length > 0) {
                   map.fitBounds(bounds, { padding: [34, 34], maxZoom: 14 });
                   }
            }, 500);

            function refreshMapSize() {
                setTimeout(() => {
                   map.invalidateSize(true);
                }, 100);

                setTimeout(() => {
                   map.invalidateSize(true);
                }, 500);

                setTimeout(() => {
                   map.invalidateSize(true);
                }, 1000);
            }

            if ('ResizeObserver' in window) {
                new ResizeObserver(refreshMapSize).observe(document.getElementById('tourismMap'));
            }

            document.querySelectorAll('[data-destination-id]').forEach((item) => {
                item.addEventListener('mouseenter', () => {
                    const marker = markerById.get(item.dataset.destinationId);
                    if (marker) {
                        marker.openPopup();
                    }
                });
            });

            function refreshMapSize() {
                requestAnimationFrame(() => {
                    map.invalidateSize(false);
                });

                setTimeout(() => map.invalidateSize(false), 200);
                setTimeout(() => map.invalidateSize(false), 600);
            }

            

            function escapeHtml(value) {
                return String(value)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }
        </script>
    @endif
@endsection





