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
                                    Route::currentRouteName() === 'admin.semester.create' ? 'Tambah Data' : 'Edit Data';
                            @endphp
                            {{ $label }} {{ $title }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="form-kriteria"
                    action="{{ isset($semester) ? route('admin.semester.update', $semester->id) : route('admin.semester.store') }}"
                    method="post">
                    @csrf
                    @isset($semester)
                        @method('PUT')
                    @endisset
                    <div class="row">
                        <div class="col-12 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                                <input type="text" class="form-control" name="tahun_ajaran" id="tahun_ajaran"
                                    placeholder="Masukkan Tahun Ajaran"
                                    value="{{ old('tahun_ajaran', $semester->tahun_ajaran ?? '') }}" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label d-block">Semester</label>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="semester" id="semesterGanjil"
                                        value="Ganjil"
                                        {{ isset($semester) && $semester->semester === 'Ganjil' ? 'checked' : '' }}
                                        required>
                                    <label class="form-check-label" for="semesterGanjil">Ganjil</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="semester" id="semesterGenap"
                                        value="Genap"
                                        {{ isset($semester) && $semester->semester === 'Genap' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="semesterGenap">Genap</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="mulai" class="form-label">Tanggal Mulai Semester</label>
                                <input type="date" class="form-control" name="mulai" id="mulai"
                                    value="{{ old('mulai', $semester->mulai ?? '') }}" />
                            </div>
                            <div class="mb-3">
                                <label for="selesai" class="form-label">Tanggal Selesai Semester</label>
                                <input type="date" class="form-control" name="selesai" id="selesai"
                                    value="{{ old('selesai', $semester->selesai ?? '') }}" />
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="aktif" id="aktif"
                                    {{ isset($semester) && $semester->aktif ? 'checked' : '' }}>
                                <label class="form-check-label" for="aktif">Aktif</label>
                            </div>
                            <div class="d-flex gap-2">
                                @include('components.form-button', [
                                    'route' => route('admin.semester.index'),
                                ])
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
