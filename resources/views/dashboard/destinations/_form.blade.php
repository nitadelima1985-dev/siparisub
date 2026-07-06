<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="name">Nama Destinasi</label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $destination->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="destination_category_id">Kategori</label>
        <select class="form-select @error('destination_category_id') is-invalid @enderror" id="destination_category_id" name="destination_category_id" required>
            <option value="">Pilih kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('destination_category_id', $destination->destination_category_id) === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('destination_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="district_id">Kecamatan</label>
        <select class="form-select @error('district_id') is-invalid @enderror" id="district_id" name="district_id" required>
            <option value="">Pilih kecamatan</option>
            @foreach($districts as $district)
                <option value="{{ $district->id }}" @selected((string) old('district_id', $destination->district_id) === (string) $district->id)>
                    {{ $district->name }}
                </option>
            @endforeach
        </select>
        @error('district_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="village_name">Desa/Kelurahan</label>
        <input class="form-control @error('village_name') is-invalid @enderror" id="village_name" name="village_name" value="{{ old('village_name', $destination->village_name) }}">
        @error('village_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label" for="latitude">Latitude</label>
        <input class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude', $destination->latitude) }}">
        @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label" for="longitude">Longitude</label>
        <input class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude', $destination->longitude) }}">
        @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="short_description">Deskripsi Singkat</label>
        <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="3" required>{{ old('short_description', $destination->short_description) }}</textarea>
        @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="full_description">Deskripsi Lengkap</label>
        <textarea class="form-control @error('full_description') is-invalid @enderror" id="full_description" name="full_description" rows="5">{{ old('full_description', $destination->full_description) }}</textarea>
        @error('full_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="address">Alamat</label>
        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address', $destination->address) }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="google_maps_url">Google Maps URL</label>
        <input class="form-control @error('google_maps_url') is-invalid @enderror" id="google_maps_url" name="google_maps_url" value="{{ old('google_maps_url', $destination->google_maps_url) }}">
        @error('google_maps_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="open_days">Hari Buka</label>
        <input class="form-control @error('open_days') is-invalid @enderror" id="open_days" name="open_days" value="{{ old('open_days', $destination->open_days) }}">
        @error('open_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="open_hours">Jam Buka</label>
        <input class="form-control @error('open_hours') is-invalid @enderror" id="open_hours" name="open_hours" value="{{ old('open_hours', $destination->open_hours) }}">
        @error('open_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="ticket_adult">Tiket Dewasa</label>
        <input class="form-control @error('ticket_adult') is-invalid @enderror" id="ticket_adult" name="ticket_adult" value="{{ old('ticket_adult', $destination->ticket_adult) }}">
        @error('ticket_adult')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="ticket_child">Tiket Anak</label>
        <input class="form-control @error('ticket_child') is-invalid @enderror" id="ticket_child" name="ticket_child" value="{{ old('ticket_child', $destination->ticket_child) }}">
        @error('ticket_child')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="parking_fee">Biaya Parkir</label>
        <input class="form-control @error('parking_fee') is-invalid @enderror" id="parking_fee" name="parking_fee" value="{{ old('parking_fee', $destination->parking_fee) }}">
        @error('parking_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="contact_phone">Telepon</label>
        <input class="form-control @error('contact_phone') is-invalid @enderror" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $destination->contact_phone) }}">
        @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="contact_email">Email</label>
        <input class="form-control @error('contact_email') is-invalid @enderror" id="contact_email" name="contact_email" value="{{ old('contact_email', $destination->contact_email) }}">
        @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="website_url">Website</label>
        <input class="form-control @error('website_url') is-invalid @enderror" id="website_url" name="website_url" value="{{ old('website_url', $destination->website_url) }}">
        @error('website_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="instagram_url">Instagram URL</label>
        <input class="form-control @error('instagram_url') is-invalid @enderror" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $destination->instagram_url) }}">
        @error('instagram_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="tiktok_url">TikTok URL</label>
        <input class="form-control @error('tiktok_url') is-invalid @enderror" id="tiktok_url" name="tiktok_url" value="{{ old('tiktok_url', $destination->tiktok_url) }}">
        @error('tiktok_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="main_attraction">Daya Tarik Utama</label>
        <textarea class="form-control @error('main_attraction') is-invalid @enderror" id="main_attraction" name="main_attraction" rows="3">{{ old('main_attraction', $destination->main_attraction) }}</textarea>
        @error('main_attraction')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="activities">Aktivitas</label>
        <textarea class="form-control @error('activities') is-invalid @enderror" id="activities" name="activities" rows="3">{{ old('activities', $destination->activities) }}</textarea>
        @error('activities')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="best_visit_time">Waktu Terbaik</label>
        <input class="form-control @error('best_visit_time') is-invalid @enderror" id="best_visit_time" name="best_visit_time" value="{{ old('best_visit_time', $destination->best_visit_time) }}">
        @error('best_visit_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="access_notes">Catatan Akses</label>
        <input class="form-control @error('access_notes') is-invalid @enderror" id="access_notes" name="access_notes" value="{{ old('access_notes', $destination->access_notes) }}">
        @error('access_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $destination->is_featured))>
            <label class="form-check-label" for="is_featured">Destinasi unggulan</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $destination->exists ? $destination->is_active : true))>
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
    </div>

    <div class="col-12 d-flex flex-wrap justify-content-between gap-2 pt-3 border-top">
        <a class="btn btn-outline-secondary" href="{{ $destination->exists ? route('dashboard.destinations.show', $destination) : route('dashboard.destinations.index') }}">Batal</a>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit" name="intent" value="draft">Simpan Draft</button>
            <button class="btn btn-primary" type="submit" name="intent" value="submit">Submit Review</button>
        </div>
    </div>
</div>
