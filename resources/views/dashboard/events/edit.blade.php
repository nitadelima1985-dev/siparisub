@extends('layouts.dashboard', ['title' => 'Edit Event'])

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-semibold mb-1">Edit Event</h1>
        <p class="text-secondary mb-0">{{ $event->title }}</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.events.update', $event) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('dashboard.events._form')
            </form>
        </div>
    </div>
@endsection
