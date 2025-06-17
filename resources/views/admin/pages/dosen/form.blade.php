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
                                    Route::currentRouteName() === 'admin.dosen.create' ? 'Tambah Data' : 'Edit Data';
                            @endphp
                            {{ $label }} {{ $title }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="form-pengguna"
                    action="{{ isset($dosen) ? route('admin.dosen.update', $dosen->id) : route('admin.dosen.store') }}"
                    method="post">
                    @csrf
                    @isset($dosen)
                        @method('PUT')
                    @endisset
                    <div class="row">
                        <div class="col-12 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="nim" class="form-label">NIP</label>
                                <input type="number" class="form-control" name="nim" id="nim"
                                    placeholder="Masukkan NIP" value="{{ old('nim', $dosen->nim ?? '') }}" />
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" id="nama"
                                    placeholder="Masukkan Nama" value="{{ old('nama', $dosen->nama ?? '') }}" />
                            </div>
                            <input type="hidden" value="dosen" id="role" name="role">
                        </div>
                        <div class="d-flex gap-2">
                            @include('components.form-button', [
                                'route' => route('admin.dosen.index'),
                            ])
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
