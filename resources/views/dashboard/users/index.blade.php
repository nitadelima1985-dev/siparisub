@extends('layouts.dashboard', ['title' => 'Manajemen Pengguna'])

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Manajemen Pengguna</h1>
            <p class="text-muted mb-0">Kelola akun operasional SIPARISUB sesuai kewenangan role.</p>
        </div>
        <a class="btn btn-success rounded-pill px-4" href="{{ route('dashboard.users.create') }}">
            <i class="fa-solid fa-user-plus me-1"></i>Tambah Pengguna
        </a>
    </div>

    <form class="row g-3 mb-4" method="GET" action="{{ route('dashboard.users.index') }}">
        <div class="col-md-4">
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, organisasi...">
        </div>
        <div class="col-md-3">
            <select class="form-select" name="role">
                <option value="">Semua role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected((string) request('role') === (string) $role->id)>{{ $role->name }}</option>
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
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Organisasi</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <div class="text-muted small">{{ $user->email }}</div>
                        </td>
                        <td><span class="badge text-bg-success">{{ $user->role?->name }}</span></td>
                        <td>{{ $user->organization?->name ?: $user->organization_name ?: '-' }}</td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'text-bg-primary' : 'text-bg-secondary' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard.users.show', $user) }}">Detail</a>
                            <a class="btn btn-sm btn-outline-success" href="{{ route('dashboard.users.edit', $user) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada pengguna yang sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
@endsection

