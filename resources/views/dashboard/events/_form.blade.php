<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="title">Judul Event</label>
        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $event->title) }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="destination_id">Destinasi Terkait</label>
        <select class="form-select @error('destination_id') is-invalid @enderror" id="destination_id" name="destination_id">
            <option value="">Tidak terkait destinasi tertentu</option>
            @foreach($destinations as $destination)
                <option value="{{ $destination->id }}" @selected((string) old('destination_id', $event->destination_id) === (string) $destination->id)>{{ $destination->name }}</option>
            @endforeach
        </select>
        @error('destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="short_description">Deskripsi Singkat</label>
        <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="3" required>{{ old('short_description', $event->short_description) }}</textarea>
        @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="full_description">Deskripsi Lengkap</label>
        <textarea class="form-control @error('full_description') is-invalid @enderror" id="full_description" name="full_description" rows="5">{{ old('full_description', $event->full_description) }}</textarea>
        @error('full_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="organizer_name">Penyelenggara</label>
        <input class="form-control @error('organizer_name') is-invalid @enderror" id="organizer_name" name="organizer_name" value="{{ old('organizer_name', $event->organizer_name) }}">
        @error('organizer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="contact_phone">Kontak</label>
        <input class="form-control @error('contact_phone') is-invalid @enderror" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $event->contact_phone) }}">
        @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="location_name">Lokasi</label>
        <input class="form-control @error('location_name') is-invalid @enderror" id="location_name" name="location_name" value="{{ old('location_name', $event->location_name) }}">
        @error('location_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="start_date">Tanggal Mulai</label>
        <input class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" type="date" value="{{ old('start_date', $event->start_date?->format('Y-m-d')) }}" required>
        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="end_date">Tanggal Selesai</label>
        <input class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" type="date" value="{{ old('end_date', $event->end_date?->format('Y-m-d')) }}">
        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="start_time">Jam Mulai</label>
        <input class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" type="time" value="{{ old('start_time', $event->start_time) }}">
        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="end_time">Jam Selesai</label>
        <input class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" type="time" value="{{ old('end_time', $event->end_time) }}">
        @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-8">
        <label class="form-label" for="cover_image">Cover/Poster Event</label>
        <input class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" type="file" accept="image/*">
        @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($event->cover_image)
            <div class="small text-secondary mt-1">Poster saat ini: {{ $event->cover_image }}</div>
        @endif
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $event->exists ? $event->is_active : true))>
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
    </div>

    <div class="col-12 d-flex flex-wrap justify-content-between gap-2 pt-3 border-top">
        <a class="btn btn-outline-secondary" href="{{ $event->exists ? route('dashboard.events.show', $event) : route('dashboard.events.index') }}">Batal</a>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit" name="intent" value="draft">Simpan Draft</button>
            <button class="btn btn-primary" type="submit" name="intent" value="submit">Submit Review</button>
        </div>
    </div>
</div>
