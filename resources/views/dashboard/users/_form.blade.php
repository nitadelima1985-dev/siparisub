@php($isEdit = $managedUser->exists)
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
        <label class="form-label" for="organization_id">Organisasi Terdaftar</label>
        <select class="form-select @error('organization_id') is-invalid @enderror" id="organization_id" name="organization_id">
            <option value="">Belum dihubungkan</option>
            @foreach($organizations as $organization)
                <option value="{{ $organization->id }}" @selected((string) old('organization_id', $managedUser->organization_id) === (string) $organization->id)>{{ $organization->name }}</option>
            @endforeach
        </select>
        @error('organization_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="organization_name">Nama Organisasi Manual</label>
        <input class="form-control @error('organization_name') is-invalid @enderror" id="organization_name" name="organization_name" value="{{ old('organization_name', $managedUser->organization_name) }}" placeholder="Fallback jika organisasi belum terdaftar">
        @error('organization_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="role_id">Role</label>
        <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
            <option value="">Pilih role</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" @selected((string) old('role_id', $managedUser->role_id) === (string) $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
        @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="profile_photo">Foto Profil</label>
        <input class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" type="file" accept="image/*">
        @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($managedUser->profile_photo)
            <div class="small text-secondary mt-1">Foto saat ini: {{ $managedUser->profile_photo }}</div>
        @endif
    </div>
    <div class="col-md-6">
        <label class="form-label" for="password">Password {{ $isEdit ? '(opsional)' : '' }}</label>
        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" @required(! $isEdit)>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" @required(! $isEdit)>
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $managedUser->is_active ?? true))>
            <label class="form-check-label" for="is_active">Akun aktif</label>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a class="btn btn-outline-secondary" href="{{ route('dashboard.users.index') }}">Batal</a>
    <button class="btn btn-success" type="submit">
        <i class="fa-solid fa-floppy-disk me-1"></i>Simpan
    </button>
</div>
