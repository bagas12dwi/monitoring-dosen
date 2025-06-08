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
                                    Route::currentRouteName() === 'admin.matkul.create' ? 'Tambah Data' : 'Edit Data';
                            @endphp
                            {{ $label }} {{ $title }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="form-kriteria"
                    action="{{ isset($matkul) ? route('admin.matkul.update', $matkul->id) : route('admin.matkul.store') }}"
                    method="post">
                    @csrf
                    @isset($matkul)
                        @method('PUT')
                    @endisset
                    <div class="row">
                        <div class="col-12 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Matakuliah</label>
                                <input type="text" class="form-control" name="kode" id="kode"
                                    placeholder="Masukkan Kode Matakuliah" value="{{ old('kode', $matkul->kode ?? '') }}" />
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Matakuliah</label>
                                <input type="text" class="form-control" name="nama" id="nama"
                                    placeholder="Masukkan Nama Matakuliah" value="{{ old('nama', $matkul->nama ?? '') }}" />
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @include('components.form-button', [
                                'route' => route('admin.matkul.index'),
                            ])
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
