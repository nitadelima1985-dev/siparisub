@extends('layouts.dashboard', ['title' => 'Tambah Organisasi'])

@section('content')
    <h1 class="h4 fw-bold mb-4">Tambah Organisasi</h1>
    <form method="POST" action="{{ route('dashboard.organizations.store') }}" enctype="multipart/form-data">
        @csrf
        @include('dashboard.organizations._form')
    </form>
@endsection
