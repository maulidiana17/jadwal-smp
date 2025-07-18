@extends('layouts.admin.dashboard')
@section('content')

    <h1>Impor Data Siswa</h1>

    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".csv" required>
        <button type="submit">Impor</button>
    </form>
@endsection
