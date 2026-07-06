<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIPARISUB - Sistem Informasi Pariwisata Subang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .navbar {
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
        }

        .navbar-brand img {
            object-fit: contain;
        }

        @media (max-width: 768px) {
             .navbar-brand {
                   gap: 8px !important;
             }

             .navbar-brand img {
                   height: 30px !important;
             }

             .navbar-brand span {
                   font-size: 22px !important;
             }
        }

        .hero {
              min-height: 82vh;
              background:
              linear-gradient(120deg, rgba(15,118,110,.82), rgba(15,23,42,.70)),
              url('<?php echo e(asset('assets/images/alun-alun-subang.jpg')); ?>');
              background-size: cover;
              background-position: center;
              color: white;
              display: flex;
              align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.22);
            color: #ecfeff;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .hero h1 {
            font-size: clamp(42px, 6vw, 72px);
            font-weight: 800;
            letter-spacing: -2px;
        }

        .hero p {
            font-size: 20px;
            color: #dbeafe;
            max-width: 720px;
        }

        .search-box {
            background: #fff;
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 20px 50px rgba(15,23,42,.18);
            margin-top: 28px;
        }

        .section-title {
            font-weight: 800;
            letter-spacing: -.5px;
        }

        .feature-card,
        .destination-card,
        .actor-card {
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 14px 35px rgba(15,23,42,.06);
            height: 100%;
            transition: .2s ease;
        }

        .feature-card:hover,
        .destination-card:hover,
        .actor-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 45px rgba(15,23,42,.10);
        }

        .icon-box {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: #ecfeff;
            color: #0f766e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
        }

        .destination-img {
            height: 190px;
            object-fit: cover;
            border-radius: 20px 20px 0 0;
        }

        .stat-section {
            background: #0f766e;
            color: white;
            border-radius: 30px;
        }

        .stat-number {
            font-size: 38px;
            font-weight: 800;
        }

        .cta {
            background: linear-gradient(120deg, #0f766e, #0f172a);
            color: white;
            border-radius: 34px;
        }
        .collab-overlay {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.28);
            color: #ffffff;
            border-radius: 18px;
            padding: 14px 18px;
            margin-top: 18px;
            font-weight: 600;
            backdrop-filter: blur(8px);
        }

        .footer-logo {
            height: 84px;
            width: auto;
        }

        footer {
            background: #a2d8d2;
            color: #043f3a;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container py-2">
        <a class="navbar-brand text-success d-flex align-items-center gap-3" href="/">
             <div class="d-flex align-items-center gap-2">
                 <img src="<?php echo e(asset('assets/images/logo-unsub.png')); ?>" alt="Logo Universitas Subang" style="height: 42px; width: auto;">
                 <img src="<?php echo e(asset('assets/images/logo-unpas.png')); ?>" alt="Logo Unpas" style="height: 42px; width: auto;">
                 <img src="<?php echo e(asset('assets/images/logo-dikti.png')); ?>" alt="Logo Dikti" style="height: 42px; width: auto;">
                 <img src="<?php echo e(asset('assets/images/logo-pemda.png')); ?>" alt="Logo Pemda" style="height: 42px; width: auto;">
             </div>

             <span class="fw-bold fs-3 ms-2">SIPARISUB</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-2">
                <li class="nav-item"><a class="nav-link fw-semibold" href="<?php echo e(route('public.destinations.index')); ?>">Destinasi</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="<?php echo e(route('public.events.index')); ?>">Event</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="<?php echo e(route('public.articles.index')); ?>">Artikel</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="#fitur">Fitur</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="#kolaborasi">Kolaborasi</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="#kontak">Kontak</a></li>
            </ul>

            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-success rounded-pill px-4">Dashboard</a>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-success rounded-pill px-4">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="container">
        <div class="hero-badge">
            <i class="fa-solid fa-people-group"></i>
            Collaborative Governance Pariwisata Kabupaten Subang
        </div>

        <h1>Jelajahi Pesona Wisata Subang dalam Satu Platform</h1>

        <p class="mt-3">
            SIPARISUB menghubungkan Dinas Pariwisata, Pokdarwis, humas destinasi,
            konten kreator, dan akademisi untuk menghadirkan informasi wisata yang
            akurat, mutakhir, dan terverifikasi.
        </p>

        <div class="collab-overlay">
           <i class="fa-solid fa-handshake-angle"></i>
              <span>Kolaborasi Dinas Pariwisata & Universitas Subang</span>
        </div>

        <div class="d-flex flex-wrap gap-3 mt-4">
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-light btn-lg rounded-pill px-4">
                    <i class="fa-solid fa-gauge-high me-2"></i>Masuk Dashboard
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn btn-light btn-lg rounded-pill px-4">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Masuk Dashboard
                </a>
            <?php endif; ?>

            <a href="<?php echo e(route('public.destinations.index')); ?>" class="btn btn-outline-light btn-lg rounded-pill px-4">
                Lihat Destinasi
            </a>
        </div>

        <form class="search-box col-lg-9" method="GET" action="{{ route('public.destinations.index') }}">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="q" class="form-control form-control-lg border-0 bg-light"
                           value="{{ request('q') }}" placeholder="Cari destinasi wisata...">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select form-select-lg border-0 bg-light">
                        <option value="">Semua kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="district" class="form-select form-select-lg border-0 bg-light">
                        <option value="">Semua kecamatan</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-success btn-lg" type="submit" aria-label="Cari destinasi">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<section id="fitur" class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="text-success fw-bold">FITUR UTAMA</span>
            <h2 class="section-title mt-2">Satu sistem untuk data, promosi, dan validasi wisata</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="feature-card p-4">
                    <div class="icon-box"><i class="fa-solid fa-map-location-dot"></i></div>
                    <h5 class="fw-bold">Direktori Destinasi</h5>
                    <p class="text-muted mb-0">Informasi objek wisata Subang tersusun berdasarkan kategori dan lokasi.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-card p-4">
                    <div class="icon-box"><i class="fa-solid fa-check-double"></i></div>
                    <h5 class="fw-bold">Validasi Dinas</h5>
                    <p class="text-muted mb-0">Konten yang tampil dapat melalui mekanisme review dan persetujuan.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-card p-4">
                    <div class="icon-box"><i class="fa-solid fa-calendar-days"></i></div>
                    <h5 class="fw-bold">Agenda Wisata</h5>
                    <p class="text-muted mb-0">Event, festival, dan agenda destinasi dapat dipublikasikan lebih terarah.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-card p-4">
                    <div class="icon-box"><i class="fa-solid fa-chart-line"></i></div>
                    <h5 class="fw-bold">Dashboard Kolaboratif</h5>
                    <p class="text-muted mb-0">Setiap aktor dapat berkontribusi sesuai peran dan kewenangannya.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="destinasi" class="py-5 bg-white">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <span class="text-success fw-bold">DESTINASI UNGGULAN</span>
                <h2 class="section-title mt-2">Wisata Populer Kabupaten Subang</h2>
            </div>
            <a href="{{ route('public.destinations.index') }}" class="btn btn-outline-success rounded-pill px-4">Lihat Semua</a>
        </div>

        <div class="row g-4">
            @forelse($featuredDestinations as $destination)
                @php($coverUrl = $destination->coverMedia?->file_path ? asset('storage/'.$destination->coverMedia->file_path) : $destination->coverMedia?->external_url)
                <div class="col-md-4">
                    <div class="destination-card">
                        @if($coverUrl)
                            <img class="w-100 destination-img" src="{{ $coverUrl }}" alt="{{ $destination->name }}">
                        @else
                            <img class="w-100 destination-img" src="{{ asset('assets/images/alun-alun-subang.jpg') }}" alt="{{ $destination->name }}">
                        @endif
                        <div class="p-4">
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge text-bg-success">{{ $destination->category?->name ?: 'Destinasi' }}</span>
                                @if($destination->district)
                                    <span class="badge text-bg-light text-dark">{{ $destination->district->name }}</span>
                                @endif
                            </div>
                            <h5 class="fw-bold">{{ $destination->name }}</h5>
                            <p class="text-muted">{{ \Illuminate\Support\Str::limit($destination->short_description, 100) }}</p>
                            <a href="{{ route('public.destinations.show', $destination->slug) }}" class="text-success fw-bold text-decoration-none">
                                Detail <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="feature-card p-5 text-center">
                        <div class="icon-box mx-auto"><i class="fa-solid fa-map-location-dot"></i></div>
                        <h5 class="fw-bold">Destinasi unggulan belum tersedia</h5>
                        <p class="text-muted mb-0">Destinasi yang sudah dipublikasikan akan tampil otomatis di bagian ini.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="text-success fw-bold">STATISTIK WISATA</span>
            <h2 class="section-title mt-2">Gambaran Ekosistem Pariwisata Subang</h2>
            <p class="text-muted">Angka berikut diperbarui otomatis dari data SIPARISUB.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="feature-card p-4 text-center">
                    <div class="icon-box mx-auto"><i class="fa-solid fa-map-location-dot"></i></div>
                    <div class="stat-number text-success">{{ $destinationCount }}</div>
                    <div class="fw-semibold">Potensi Destinasi</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-card p-4 text-center">
                    <div class="icon-box mx-auto"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-number text-success">{{ $collaborationActorCount }}</div>
                    <div class="fw-semibold">Aktor Kolaborasi</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-card p-4 text-center">
                    <div class="icon-box mx-auto"><i class="fa-solid fa-calendar-check"></i></div>
                    <div class="stat-number text-success">{{ $yearlyEventCount }}</div>
                    <div class="fw-semibold">Agenda Wisata/Tahun</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="feature-card p-4 text-center">
                    <div class="icon-box mx-auto"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="stat-number text-success">{{ $validatedContentPercentage }}%</div>
                    <div class="fw-semibold">Konten Tervalidasi</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="kolaborasi" class="py-5">
    <div class="container py-4">
        <div class="stat-section p-5">
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="stat-number">6</div>
                    <div>Aktor Kolaborasi</div>
                </div>
                <div class="col-md-3">
                    <div class="stat-number">1</div>
                    <div>Platform Terintegrasi</div>
                </div>
                <div class="col-md-3">
                    <div class="stat-number">100%</div>
                    <div>Berbasis Verifikasi</div>
                </div>
                <div class="col-md-3">
                    <div class="stat-number">24/7</div>
                    <div>Akses Informasi</div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 mb-4">
            <span class="text-success fw-bold">MODEL KOLABORASI</span>
            <h2 class="section-title mt-2">Siapa saja yang terlibat?</h2>
        </div>

        <div class="row g-4">
            <?php $__currentLoopData = [
                ['Dinas Pariwisata', 'Verifikasi, validasi, dan publikasi data resmi.', 'fa-building-columns'],
                ['Pokdarwis', 'Pembaruan data lapangan dan potensi wisata lokal.', 'fa-people-roof'],
                ['Humas Destinasi', 'Informasi operasional, fasilitas, dan promosi destinasi.', 'fa-bullhorn'],
                ['Konten Kreator', 'Artikel, foto, video, dan promosi digital.', 'fa-camera-retro'],
                ['Dosen/Akademisi', 'Review substansi, kurasi, riset, dan evaluasi.', 'fa-graduation-cap'],
                ['Masyarakat', 'Pengguna informasi dan pemberi masukan.', 'fa-users']
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4">
                    <div class="actor-card p-4">
                        <div class="icon-box"><i class="fa-solid <?php echo e($actor[2]); ?>"></i></div>
                        <h5 class="fw-bold"><?php echo e($actor[0]); ?></h5>
                        <p class="text-muted mb-0"><?php echo e($actor[1]); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="cta p-5 text-center">
            <h2 class="fw-bold mb-3">Siap Mengelola Pariwisata Subang Secara Kolaboratif?</h2>
            <p class="mb-4 text-white-50">
                Masuk ke dashboard untuk mengelola data destinasi, konten, dan validasi informasi wisata.
            </p>
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-light btn-lg rounded-pill px-4">Masuk Dashboard</a>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn btn-light btn-lg rounded-pill px-4">Login Sekarang</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<footer id="kontak" class="py-5">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="<?php echo e(asset('assets/images/logo-siparsub-tanpabg.png')); ?>" alt="Logo Siparsub" class="footer-logo">
                    <div>
                        <h5 class="text-blue fw-bold mb-0">SIPARISUB</h5>
                        <small>Sistem Informasi Pariwisata Kabupaten Subang</small>
                    </div>
                </div>
                <p class="mb-0">
                    Platform collaborative governance untuk pengelolaan data,
                    promosi, validasi, dan publikasi informasi pariwisata Kabupaten Subang.
                </p>
            </div>

            <div class="col-md-4">
                <h6 class="text-blue fw-bold">Identitas Kelembagaan</h6>
                <p class="mb-1">Universitas Subang</p>
                <p class="mb-1">Universitas Pasundan</p>
                <p class="mb-0">Kolaborasi dengan Dinas Pariwisata Kabupaten Subang dan aktor pariwisata daerah.</p>
            </div>

            <div class="col-md-3">
                <h6 class="text-blue fw-bold">Aktor Kolaborasi</h6>
                <p class="mb-1">Dinas Pariwisata</p>
                <p class="mb-1">Pokdarwis</p>
                <p class="mb-1">Humas Destinasi</p>
                <p class="mb-1">Konten Kreator</p>
                <p class="mb-0">Dosen/Akademisi</p>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex justify-content-between flex-wrap gap-3 small">
            <div>© <?php echo e(date('Y')); ?> SIPARISUB · Universitas Subang</div>
            <div> Jl. R.A. Kartini Km 3 Subang    email: endangirawan.ei@unsub.ac.id</div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\AdmiN\Documents\Codex\2026-07-05\SIPARISUB\resources\views/public/home.blade.php ENDPATH**/ ?>




