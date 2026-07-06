<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            color: #0f172a;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .navbar {
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
        }

        .page-hero {
            background: linear-gradient(120deg, #0f766e, #0f172a);
            color: #fff;
            border-radius: 0 0 34px 34px;
        }

        .section-title {
            font-weight: 800;
            letter-spacing: -.5px;
        }

        .destination-card,
        .info-card {
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 14px 35px rgba(15,23,42,.06);
        }

        .destination-card {
            height: 100%;
            transition: .2s ease;
            overflow: hidden;
        }

        .destination-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 45px rgba(15,23,42,.10);
        }

        .destination-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #e2e8f0;
        }

        .gallery-img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            border-radius: 18px;
        }

        .filter-box {
            background: #fff;
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 18px 45px rgba(15,23,42,.10);
        }

        footer {
            background: #0f172a;
            color: #cbd5e1;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container py-2">
            <a class="navbar-brand fw-bold fs-3 text-success d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo-unsub.png') }}" alt="Logo Unsub" style="height: 42px; width: auto;">
                <span>SIPARISUB</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-2">
                    <li class="nav-item"><a class="nav-link fw-semibold {{ request()->routeIs('public.destinations.*') ? 'active text-success' : '' }}" href="{{ route('public.destinations.index') }}">Destinasi</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold {{ request()->routeIs('public.events.*') ? 'active text-success' : '' }}" href="{{ route('public.events.index') }}">Event</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold {{ request()->routeIs('public.articles.*') ? 'active text-success' : '' }}" href="{{ route('public.articles.index') }}">Artikel</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="{{ route('home') }}#fitur">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="{{ route('home') }}#kolaborasi">Kolaborasi</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="{{ route('home') }}#kontak">Kontak</a></li>
                </ul>

                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-success rounded-pill px-4">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-success rounded-pill px-4">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="py-4 mt-5">
        <div class="container d-flex justify-content-between flex-wrap gap-3 small">
            <div>&copy; {{ date('Y') }} SIPARISUB - Sistem Informasi Pariwisata Kabupaten Subang</div>
            <div>Collaborative Governance - Pariwisata - Transformasi Digital</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





