@extends('layouts.app')
@section('title', $title)

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container-fluid">
        <h3 class="text-dark mb-4">{{ $title }}</h3>
        <div class="card shadow">
            <div class="card-header py-3">
                <div class="row text-sm-start text-center">
                    <div class="col-sm-5 col-12 mb-md-0 mb-3">
                        <p class="text-primary fw-bold m-0 mt-2">
                            @php
                                $label =
                                    Route::currentRouteName() === 'admin.kelas.create' ? 'Tambah Data' : 'Edit Data';
                            @endphp
                            {{ $label }} {{ $title }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="form-kriteria"
                    action="{{ isset($kelas) ? route('admin.kelas.update', $kelas->id) : route('admin.kelas.store') }}"
                    method="post">
                    @csrf
                    @isset($kelas)
                        @method('PUT')
                    @endisset
                    <div class="row">
                        <div class="col-12 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="angkatan" class="form-label">Angkatan</label>
                                <input type="text" class="form-control" name="angkatan" id="angkatan"
                                    placeholder="Masukkan ANGKATAN" value="{{ old('angkatan', $kelas->angkatan ?? '') }}" />
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Kelas</label>
                                <input type="text" class="form-control" name="nama" id="nama"
                                    placeholder="Masukkan Nama Kelas Cth: A, B, C, D, E, F"
                                    value="{{ old('nama', $kelas->nama ?? '') }}" />
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @include('components.form-button', [
                                'route' => route('admin.kelas.index'),
                            ])
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
