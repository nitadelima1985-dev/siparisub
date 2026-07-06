@extends('layouts.dashboard', ['title' => 'Edit Pengguna'])

@section('content')
    <h1 class="h4 fw-bold mb-4">Edit Pengguna</h1>
    <form method="POST" action="{{ route('dashboard.users.update', $managedUser) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('dashboard.users._form')
    </form>
@endsection
