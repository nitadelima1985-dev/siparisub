<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Nama Organisasi</label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $organization->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="organization_type">Tipe Organisasi</label>
        <select class="form-select @error('organization_type') is-invalid @enderror" id="organization_type" name="organization_type" required>
            <option value="">Pilih tipe</option>
            @foreach($types as $type)
                <option value="{{ $type->value }}" @selected(old('organization_type', $organization->organization_type?->value) === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
        @error('organization_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="description">Deskripsi</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $organization->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="address">Alamat</label>
        <input class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $organization->address) }}">
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="phone">Telepon</label>
        <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $organization->phone) }}">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="email">Email</label>
        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $organization->email) }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="website_url">Website</label>
        <input class="form-control @error('website_url') is-invalid @enderror" id="website_url" name="website_url" type="url" value="{{ old('website_url', $organization->website_url) }}">
        @error('website_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="pic_name">Nama PIC</label>
        <input class="form-control @error('pic_name') is-invalid @enderror" id="pic_name" name="pic_name" value="{{ old('pic_name', $organization->pic_name) }}">
        @error('pic_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="pic_phone">Telepon PIC</label>
        <input class="form-control @error('pic_phone') is-invalid @enderror" id="pic_phone" name="pic_phone" value="{{ old('pic_phone', $organization->pic_phone) }}">
        @error('pic_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="logo">Logo</label>
        <input class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" type="file" accept="image/*">
        @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($organization->logo)
            <div class="small text-secondary mt-1">Logo saat ini: {{ $organization->logo }}</div>
        @endif
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $organization->is_active ?? true))>
            <label class="form-check-label" for="is_active">Organisasi aktif</label>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a class="btn btn-outline-secondary" href="{{ route('dashboard.organizations.index') }}">Batal</a>
    <button class="btn btn-success" type="submit">
        <i class="fa-solid fa-floppy-disk me-1"></i>Simpan
    </button>
</div>
