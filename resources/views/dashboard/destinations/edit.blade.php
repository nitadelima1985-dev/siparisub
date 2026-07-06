@extends('layouts.dashboard', ['title' => 'Edit Destinasi'])

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-semibold mb-1">Edit Destinasi</h1>
        <p class="text-secondary mb-0">{{ $destination->name }}</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.destinations.update', $destination) }}">
                @csrf
                @method('PUT')
                @include('dashboard.destinations._form')
            </form>
        </div>
    </div>
@endsection
