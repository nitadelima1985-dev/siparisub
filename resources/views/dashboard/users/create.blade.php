@extends('layouts.dashboard', ['title' => 'Tambah Pengguna'])

@section('content')
    <h1 class="h4 fw-bold mb-4">Tambah Pengguna</h1>
    <form method="POST" action="{{ route('dashboard.users.store') }}" enctype="multipart/form-data">
        @csrf
        @include('dashboard.users._form')
    </form>
@endsection
