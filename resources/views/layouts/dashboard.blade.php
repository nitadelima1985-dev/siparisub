<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: linear-gradient(180deg, #0f766e 0%, #0f172a 100%);
            --primary-soft: #ecfeff;
            --text-muted: #64748b;
        }

        body {
            background: #f8fafc;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .admin-wrapper {
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            color: #fff;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            padding: 22px 18px;
            overflow-y: auto;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: rgba(255,255,255,.16);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 700;
            line-height: 1.1;
        }

        .brand-subtitle {
            font-size: 12px;
            opacity: .75;
        }

        .sidebar-label {
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            opacity: .65;
            margin: 18px 12px 8px;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,.82);
            border-radius: 12px;
            padding: 11px 13px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 11px;
            font-weight: 500;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,.14);
            color: #fff;
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }

        .main-area {
            margin-left: 280px;
            width: calc(100% - 280px);
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 28px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .content-area {
            padding: 28px;
        }

        .page-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
        }

        .user-badge {
            background: var(--primary-soft);
            color: #0f766e;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-logout {
            border-radius: 999px;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .admin-wrapper {
                display: block;
            }

            .main-area {
                margin-left: 0;
                width: 100%;
            }

            .content-area {
                padding: 18px;
            }
        }
    </style>
</head>

<body>
<div class="admin-wrapper">
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="text-white text-decoration-none">
            <div class="brand-box">
                <div class="brand-icon">
                    <i class="fa-solid fa-mountain-sun"></i>
                </div>
                <div>
                    <div class="brand-title">SIPARISUB</div>
                    <div class="brand-subtitle">Tourism Governance</div>
                </div>
            </div>
        </a>

        <div class="sidebar-label">Menu Utama</div>

        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Ringkasan</span>
            </a>

            @if(auth()->user()->hasRole('super_admin', 'admin_dinas'))
                <a class="nav-link {{ request()->routeIs('dashboard.users.*') ? 'active' : '' }}" href="{{ route('dashboard.users.index') }}">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Manajemen Pengguna</span>
                </a>
                <a class="nav-link {{ request()->routeIs('dashboard.organizations.*') ? 'active' : '' }}" href="{{ route('dashboard.organizations.index') }}">
                    <i class="fa-solid fa-building-user"></i>
                    <span>Organisasi</span>
                </a>
                <a class="nav-link {{ request()->routeIs('dashboard.reports.*') ? 'active' : '' }}" href="{{ route('dashboard.reports.index') }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Statistik & Laporan</span>
                </a>
            @endif

            @if(auth()->user()->hasRole('super_admin', 'admin_dinas', 'admin_pokdarwis', 'admin_humas', 'reviewer_akademik'))
                <a class="nav-link {{ request()->routeIs('dashboard.destinations.*') ? 'active' : '' }}" href="{{ route('dashboard.destinations.index') }}">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>Data Destinasi</span>
                </a>
            @endif

            @if(auth()->user()->hasRole('super_admin', 'admin_dinas', 'admin_pokdarwis', 'admin_humas', 'konten_kreator', 'reviewer_akademik'))
                <a class="nav-link {{ request()->routeIs('dashboard.articles.*') ? 'active' : '' }}" href="{{ route('dashboard.articles.index') }}">
                    <i class="fa-solid fa-newspaper"></i>
                    <span>Artikel</span>
                </a>
            @endif
        </nav>

        <div class="sidebar-label">Kolaborasi</div>

        <nav class="nav flex-column">
            @if(auth()->user()->hasRole('super_admin', 'admin_dinas', 'reviewer_akademik'))
                <a class="nav-link {{ request()->routeIs('dashboard.approvals.*') ? 'active' : '' }}" href="{{ route('dashboard.approvals.index') }}">
                    <i class="fa-solid fa-check-double"></i>
                    <span>Approval</span>
                </a>
            @endif
            @if(auth()->user()->hasRole('super_admin', 'admin_dinas', 'admin_pokdarwis', 'admin_humas', 'konten_kreator', 'reviewer_akademik'))
                <a class="nav-link {{ request()->routeIs('dashboard.events.*') ? 'active' : '' }}" href="{{ route('dashboard.events.index') }}">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Event Wisata</span>
                </a>
            @endif
        </nav>
    </aside>

    <main class="main-area">
        <div class="topbar d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="mb-0 fw-bold">{{ $title ?? 'Dashboard' }}</h5>
                <div class="text-muted small">Kelola informasi pariwisata Kabupaten Subang secara kolaboratif.</div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @php
                    $dashboardNotifications = \Illuminate\Support\Facades\Schema::hasTable('notifications')
                        ? auth()->user()->notifications()->latest()->limit(5)->get()
                        : collect();
                    $dashboardUnreadNotificationCount = \Illuminate\Support\Facades\Schema::hasTable('notifications')
                        ? auth()->user()->notifications()->whereNull('read_at')->count()
                        : 0;
                @endphp

                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi">
                        <i class="fa-solid fa-bell"></i>
                        @if($dashboardUnreadNotificationCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $dashboardUnreadNotificationCount }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0" style="width: 360px; max-width: calc(100vw - 32px);">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <span class="fw-semibold">Notifikasi</span>
                            <a class="small text-decoration-none" href="{{ route('dashboard.notifications.index') }}">Lihat semua</a>
                        </div>
                        @forelse($dashboardNotifications as $notification)
                            <form method="POST" action="{{ route('dashboard.notifications.read', $notification) }}" class="border-bottom">
                                @csrf
                                <button class="dropdown-item text-wrap py-3 {{ $notification->read_at ? '' : 'bg-light' }}" type="submit">
                                    <div class="fw-semibold small">{{ $notification->title }}</div>
                                    <div class="text-secondary small">{{ \Illuminate\Support\Str::limit($notification->message, 90) }}</div>
                                    <div class="text-muted small mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                </button>
                            </form>
                        @empty
                            <div class="px-3 py-4 text-center text-secondary small">Belum ada notifikasi.</div>
                        @endforelse
                    </div>
                </div>

                <a class="user-badge text-decoration-none" href="{{ route('dashboard.profile') }}">
                    <i class="fa-solid fa-user me-1"></i>
                    {{ auth()->user()->name }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm btn-logout" type="submit">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-1"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="page-card p-4">
                @yield('content')
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



