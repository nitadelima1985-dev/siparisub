@extends('layouts.dashboard', ['title' => 'Detail Pengguna'])

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">{{ $managedUser->name }}</h1>
            <div class="text-muted">{{ $managedUser->email }}</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('dashboard.users.index') }}">Kembali</a>
            @can('update', $managedUser)
                <a class="btn btn-success" href="{{ route('dashboard.users.edit', $managedUser) }}">Edit</a>
            @endcan
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="border rounded-3 p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-4">
                    @if($managedUser->profile_photo)
                        <img class="rounded-circle object-fit-cover" style="width: 76px; height: 76px;" src="{{ asset('storage/'.$managedUser->profile_photo) }}" alt="{{ $managedUser->name }}">
                    @else
                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 76px; height: 76px;">
                            <i class="fa-solid fa-user fs-2"></i>
                        </div>
                    @endif
                    <div>
                        <div class="fw-bold fs-5">{{ $managedUser->name }}</div>
                        <span class="badge text-bg-success">{{ $managedUser->role?->name }}</span>
                        <span class="badge {{ $managedUser->is_active ? 'text-bg-primary' : 'text-bg-secondary' }}">{{ $managedUser->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Telepon</div>
                        <div class="fw-semibold">{{ $managedUser->phone ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Organisasi</div>
                        <div class="fw-semibold">{{ $managedUser->organization?->name ?: $managedUser->organization_name ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Dibuat</div>
                        <div class="fw-semibold">{{ $managedUser->created_at?->translatedFormat('d F Y H:i') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Terakhir Update</div>
                        <div class="fw-semibold">{{ $managedUser->updated_at?->translatedFormat('d F Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="border rounded-3 p-4 mb-4">
                <h2 class="h6 fw-bold mb-3">Aktivitas Konten</h2>
                <div class="row g-3 text-center">
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-success">{{ $managedUser->createdDestinations->count() }}</div>
                        <div class="small text-muted">Destinasi</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-success">{{ $managedUser->createdEvents->count() }}</div>
                        <div class="small text-muted">Event</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-success">{{ $managedUser->createdArticles->count() }}</div>
                        <div class="small text-muted">Artikel</div>
                    </div>
                </div>
            </div>

            @can('manageAccount', $managedUser)
                <div class="border rounded-3 p-4 mb-4">
                    <h2 class="h6 fw-bold mb-3">Status Akun</h2>
                    <form method="POST" action="{{ route('dashboard.users.toggle-active', $managedUser) }}">
                        @csrf
                        @method('PATCH')
                        <button class="btn {{ $managedUser->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} w-100" type="submit">
                            {{ $managedUser->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                        </button>
                    </form>
                </div>

                <div class="border rounded-3 p-4">
                    <h2 class="h6 fw-bold mb-3">Reset Password</h2>
                    <form method="POST" action="{{ route('dashboard.users.reset-password', $managedUser) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label" for="password">Password Baru</label>
                            <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                            <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required>
                        </div>
                        <button class="btn btn-outline-success w-100" type="submit">Reset Password</button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
@endsection


