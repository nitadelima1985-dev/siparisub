@extends('layouts.dashboard', ['title' => 'Ganti Password'])

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Ganti Password</h1>
            <p class="text-muted mb-0">Gunakan password yang kuat untuk menjaga keamanan akun.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('dashboard.profile') }}">Kembali ke Profil</a>
    </div>

    <form method="POST" action="{{ route('dashboard.profile.password.update') }}" class="col-lg-7">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label" for="current_password">Password Saat Ini</label>
            <input class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" type="password" required>
            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Password Baru</label>
            <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
            <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required>
        </div>
        <button class="btn btn-success" type="submit"><i class="fa-solid fa-key me-1"></i>Perbarui Password</button>
    </form>
@endsection
