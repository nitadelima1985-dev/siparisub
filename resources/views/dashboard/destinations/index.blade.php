@extends('layouts.dashboard', ['title' => 'Data Destinasi'])

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">Data Destinasi</h1>
            <p class="text-secondary mb-0">Kelola destinasi wisata dan status workflow internal.</p>
        </div>
        @can('create', App\Models\Destination::class)
            <a class="btn btn-primary" href="{{ route('dashboard.destinations.create') }}">Tambah Destinasi</a>
        @endcan
    </div>

    @if(auth()->user()->hasRole('super_admin', 'admin_dinas', 'reviewer_akademik'))
        <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
            <span class="small text-secondary fw-semibold">Antrean Review:</span>
            @foreach($reviewQueueStatuses as $queueStatus)
                <a class="btn btn-sm {{ request('status') === $queueStatus->value ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('dashboard.destinations.index', ['status' => $queueStatus->value]) }}">
                    {{ $queueStatus->label() }}
                </a>
            @endforeach
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard.destinations.index') }}">Semua</a>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET" action="{{ route('dashboard.destinations.index') }}">
                <div class="col-md-4">
                    <label class="form-label" for="category">Kategori</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Semua kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="district">Kecamatan</label>
                    <select class="form-select" id="district" name="district">
                        <option value="">Semua kecamatan</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" @selected((string) request('district') === (string) $district->id)>
                                {{ $district->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-outline-primary w-100" type="submit">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Destinasi</th>
                        <th>Kategori</th>
                        <th>Kecamatan</th>
                        <th>Status</th>
                        <th>Pembuat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($destinations as $destination)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $destination->name }}</div>
                                <div class="small text-secondary">{{ $destination->village_name ?: $destination->address }}</div>
                            </td>
                            <td>{{ $destination->category?->name }}</td>
                            <td>{{ $destination->district?->name }}</td>
                            <td>
                                <span class="badge text-bg-secondary">{{ $destination->workflow_status->label() }}</span>
                            </td>
                            <td>{{ $destination->creator?->name }}</td>
                            <td class="text-end">
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('dashboard.destinations.show', $destination) }}">Detail</a>
                                @can('update', $destination)
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('dashboard.destinations.edit', $destination) }}">Edit</a>
                                @endcan
                                @can('delete', $destination)
                                    <form class="d-inline" method="POST" action="{{ route('dashboard.destinations.destroy', $destination) }}" onsubmit="return confirm('Yakin ingin menghapus destinasi ini? Data media dan riwayat approval terkait juga akan dihapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Belum ada destinasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">
            {{ $destinations->links() }}
        </div>
    </div>
@endsection
