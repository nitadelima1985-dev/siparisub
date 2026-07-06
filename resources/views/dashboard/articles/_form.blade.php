<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="title">Judul Artikel</label>
        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $article->title) }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="article_category_id">Kategori</label>
        <select class="form-select @error('article_category_id') is-invalid @enderror" id="article_category_id" name="article_category_id" required>
            <option value="">Pilih kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('article_category_id', $article->article_category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('article_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="destination_id">Destinasi Terkait</label>
        <select class="form-select @error('destination_id') is-invalid @enderror" id="destination_id" name="destination_id">
            <option value="">Tidak terkait destinasi tertentu</option>
            @foreach($destinations as $destination)
                <option value="{{ $destination->id }}" @selected((string) old('destination_id', $article->destination_id) === (string) $destination->id)>{{ $destination->name }}</option>
            @endforeach
        </select>
        @error('destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="featured_image">Featured Image</label>
        <input class="form-control @error('featured_image') is-invalid @enderror" id="featured_image" name="featured_image" type="file" accept="image/*">
        @error('featured_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($article->featured_image)
            <div class="small text-secondary mt-1">Gambar saat ini: {{ $article->featured_image }}</div>
        @endif
    </div>

    <div class="col-12">
        <label class="form-label" for="excerpt">Excerpt</label>
        <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $article->excerpt) }}</textarea>
        @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="content">Konten</label>
        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10" required>{{ old('content', $article->content) }}</textarea>
        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="source_name">Nama Sumber</label>
        <input class="form-control @error('source_name') is-invalid @enderror" id="source_name" name="source_name" value="{{ old('source_name', $article->source_name) }}">
        @error('source_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="source_url">URL Sumber</label>
        <input class="form-control @error('source_url') is-invalid @enderror" id="source_url" name="source_url" value="{{ old('source_url', $article->source_url) }}">
        @error('source_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $article->is_featured))>
            <label class="form-check-label" for="is_featured">Artikel unggulan</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $article->exists ? $article->is_active : true))>
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
    </div>

    <div class="col-12 d-flex flex-wrap justify-content-between gap-2 pt-3 border-top">
        <a class="btn btn-outline-secondary" href="{{ $article->exists ? route('dashboard.articles.show', $article) : route('dashboard.articles.index') }}">Batal</a>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit" name="intent" value="draft">Simpan Draft</button>
            <button class="btn btn-primary" type="submit" name="intent" value="submit">Submit Review</button>
        </div>
    </div>
</div>
