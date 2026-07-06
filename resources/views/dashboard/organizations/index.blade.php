@extends('layouts.dashboard', ['title' => 'Manajemen Organisasi'])

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Manajemen Organisasi</h1>
            <p class="text-muted mb-0">Kelola aktor kolaborasi pariwisata dalam ekosistem SIPARISUB.</p>
        </div>
        <a class="btn btn-success rounded-pill px-4" href="{{ route('dashboard.organizations.create') }}">
            <i class="fa-solid fa-building-circle-plus me-1"></i>Tambah Organisasi
        </a>
    </div>

    <form class="row g-3 mb-4" method="GET" action="{{ route('dashboard.organizations.index') }}">
        <div class="col-md-4">
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari nama organisasi...">
        </div>
        <div class="col-md-3">
            <select class="form-select" name="type">
                <option value="">Semua tipe</option>
                @foreach($types as $type)
                    <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" name="is_active">
                <option value="">Semua status</option>
                <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-outline-success" type="submit"><i class="fa-solid fa-filter me-1"></i>Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Organisasi</th>
                    <th>Tipe</th>
                    <th>PIC</th>
                    <th>User</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($organizations as $organization)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $organization->name }}</div>
                            <div class="text-muted small">{{ $organization->email ?: $organization->phone ?: '-' }}</div>
                        </td>
                        <td><span class="badge text-bg-success">{{ $organization->organization_type->label() }}</span></td>
                        <td>{{ $organization->pic_name ?: '-' }}</td>
                        <td>{{ $organization->users_count }}</td>
                        <td><span class="badge {{ $organization->is_active ? 'text-bg-primary' : 'text-bg-secondary' }}">{{ $organization->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard.organizations.show', $organization) }}">Detail</a>
                            <a class="btn btn-sm btn-outline-success" href="{{ route('dashboard.organizations.edit', $organization) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada organisasi yang sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $organizations->links() }}
@endsection
