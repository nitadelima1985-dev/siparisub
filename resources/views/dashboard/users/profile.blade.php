@extends('layouts.dashboard', ['title' => 'Profil Saya'])

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Profil Saya</h1>
            <p class="text-muted mb-0">Perbarui identitas dan informasi kontak akun Anda.</p>
        </div>
        <a class="btn btn-outline-success" href="{{ route('dashboard.profile.password') }}">Ganti Password</a>
    </div>

    <form method="POST" action="{{ route('dashboard.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">Nama</label>
                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $managedUser->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $managedUser->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="phone">Telepon</label>
                <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $managedUser->phone) }}">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="organization_name">Organisasi Manual</label>
                <input class="form-control @error('organization_name') is-invalid @enderror" id="organization_name" name="organization_name" value="{{ old('organization_name', $managedUser->organization_name) }}">
                @error('organization_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="profile_photo">Foto Profil</label>
                <input class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" type="file" accept="image/*">
                @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Role</label>
                <input class="form-control" value="{{ $managedUser->role?->name }}" disabled>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4">
            <button class="btn btn-success" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan Profil</button>
        </div>
    </form>
@endsection



