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
                                    Route::currentRouteName() === 'admin.pertanyaan.create'
                                        ? 'Tambah Data'
                                        : 'Edit Data';
                            @endphp
                            {{ $label }} {{ $title }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="form-kriteria"
                    action="{{ isset($pertanyaan) ? route('admin.pertanyaan.update', $pertanyaan->id) : route('admin.pertanyaan.store') }}"
                    method="post">
                    @csrf
                    @isset($pertanyaan)
                        @method('PUT')
                    @endisset
                    <div class="row">
                        <div class="col-12 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="kriteria_id" class="form-label">Kriteria</label>
                                <select class="form-select form-select" name="kriteria_id" id="kriteria_id">
                                    <option value="">Pilih Kriteria</option>
                                    @foreach ($kriterias as $kriteria)
                                        <option value="{{ $kriteria->id }}"
                                            {{ old('kriteria_id', $pertanyaan->kriteria_id ?? '') == $kriteria->id ? 'selected' : '' }}>
                                            {{ $kriteria->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="pertanyaan" class="form-label">Pertanyaan</label>
                                <input type="text" class="form-control" name="pertanyaan" id="pertanyaan"
                                    placeholder="Masukkan Pertanyaan"
                                    value="{{ old('pertanyaan', $pertanyaan->pertanyaan ?? '') }}" />
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @include('components.form-button', [
                                'route' => route('admin.pertanyaan.index'),
                            ])
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
