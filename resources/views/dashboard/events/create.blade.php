@extends('layouts.dashboard', ['title' => 'Tambah Event'])

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-semibold mb-1">Tambah Event</h1>
        <p class="text-secondary mb-0">Simpan sebagai draft atau submit untuk review.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.events.store') }}" enctype="multipart/form-data">
                @csrf
                @include('dashboard.events._form')
            </form>
        </div>
    </div>
@endsection
