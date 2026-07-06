@extends('layouts.dashboard', ['title' => 'Detail Organisasi'])

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            @if($organization->logo)
                <img class="rounded-3 object-fit-cover border" style="width: 76px; height: 76px;" src="{{ asset('storage/'.$organization->logo) }}" alt="{{ $organization->name }}">
            @else
                <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 76px; height: 76px;">
                    <i class="fa-solid fa-building fs-2"></i>
                </div>
            @endif
            <div>
                <h1 class="h4 fw-bold mb-1">{{ $organization->name }}</h1>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge text-bg-success">{{ $organization->organization_type->label() }}</span>
                    <span class="badge {{ $organization->is_active ? 'text-bg-primary' : 'text-bg-secondary' }}">{{ $organization->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->hasRole('super_admin', 'admin_dinas'))
                <a class="btn btn-outline-secondary" href="{{ route('dashboard.organizations.index') }}">Kembali</a>
                <a class="btn btn-success" href="{{ route('dashboard.organizations.edit', $organization) }}">Edit</a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="border rounded-3 p-4 h-100">
                <h2 class="h6 fw-bold mb-3">Profil Organisasi</h2>
                <div class="text-muted mb-4" style="white-space: pre-wrap;">{{ $organization->description ?: 'Belum ada deskripsi organisasi.' }}</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Alamat</div>
                        <div class="fw-semibold">{{ $organization->address ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Telepon</div>
                        <div class="fw-semibold">{{ $organization->phone ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Email</div>
                        <div class="fw-semibold">{{ $organization->email ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Website</div>
                        @if($organization->website_url)
                            <a class="fw-semibold text-success" href="{{ $organization->website_url }}" target="_blank" rel="noopener">{{ $organization->website_url }}</a>
                        @else
                            <div class="fw-semibold">-</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">PIC</div>
                        <div class="fw-semibold">{{ $organization->pic_name ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Telepon PIC</div>
                        <div class="fw-semibold">{{ $organization->pic_phone ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="border rounded-3 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 fw-bold mb-0">User Tergabung</h2>
                    <span class="badge text-bg-light text-dark">{{ $organization->users->count() }} user</span>
                </div>
                <div class="d-grid gap-3">
                    @forelse($organization->users as $user)
                        <div class="d-flex justify-content-between align-items-center gap-3 border-bottom pb-3">
                            <div>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <div class="small text-muted">{{ $user->email }}</div>
                            </div>
                            <span class="badge text-bg-success">{{ $user->role?->name }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">Belum ada user yang tergabung dalam organisasi ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
