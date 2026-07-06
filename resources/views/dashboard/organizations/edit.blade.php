@extends('layouts.dashboard', ['title' => 'Edit Organisasi'])

@section('content')
    <h1 class="h4 fw-bold mb-4">Edit Organisasi</h1>
    <form method="POST" action="{{ route('dashboard.organizations.update', $organization) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('dashboard.organizations._form')
    </form>
@endsection
