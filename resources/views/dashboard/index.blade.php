@extends('layouts.dashboard', ['title' => 'Dashboard'])

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">Dashboard</h1>
            <p class="text-secondary mb-0">
                Selamat datang, {{ $user->name }}.
                Role aktif: <span class="fw-semibold">{{ $role?->label() ?? 'Belum ada role' }}</span>
            </p>
        </div>
        <div class="text-end small text-secondary">
            <div>{{ now()->translatedFormat('d F Y') }}</div>
            <div>SIPARISUB Tourism Governance</div>
        </div>
    </div>

    @if($stats)
        <div class="row g-3 mb-4">
            @foreach($stats as $stat)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 {{ $stat['variant'] }} d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                <i class="fa-solid {{ $stat['icon'] }} fs-5"></i>
                            </div>
                            <div>
                                <div class="text-secondary small fw-semibold text-uppercase">{{ $stat['label'] }}</div>
                                <div class="fs-3 fw-bold lh-1">{{ number_format($stat['value']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning">User ini belum memiliki statistik dashboard aktif.</div>
    @endif

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="h5 fw-semibold mb-0">{{ $primaryTitle }}</h2>
                        <span class="badge text-bg-light">Ringkasan</span>
                    </div>

                    @forelse($primaryItems as $item)
                        @include('dashboard.partials.content-list-item', ['item' => $item])
                    @empty
                        <div class="text-center text-secondary py-4">
                            <i class="fa-solid fa-inbox fs-3 mb-2 d-block"></i>
                            Belum ada data untuk ditampilkan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="h5 fw-semibold mb-0">{{ $secondaryTitle }}</h2>
                        <span class="badge text-bg-light">Prioritas</span>
                    </div>

                    @forelse($secondaryItems as $item)
                        @include('dashboard.partials.content-list-item', ['item' => $item])
                    @empty
                        <div class="text-center text-secondary py-4">
                            <i class="fa-solid fa-circle-check fs-3 mb-2 d-block"></i>
                            Tidak ada item yang membutuhkan perhatian saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if($menus)
        <div class="mt-4">
            <h2 class="h5 fw-semibold mb-3">Menu Role</h2>
            <div class="row g-3">
                @foreach($menus as $menu)
                    <div class="col-md-6 col-xl-3">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <div class="text-secondary small fw-semibold text-uppercase mb-1">Akses</div>
                            <div class="fw-semibold">{{ $menu }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
