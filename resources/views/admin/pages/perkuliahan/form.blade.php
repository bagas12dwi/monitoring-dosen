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
                                    Route::currentRouteName() === 'admin.perkuliahan.create'
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
                    action="{{ isset($perkuliahan) ? route('admin.perkuliahan.update', $perkuliahan->id) : route('admin.perkuliahan.store') }}"
                    method="post">
                    @csrf
                    @isset($perkuliahan)
                        @method('PUT')
                    @endisset
                    <div class="row">
                        <div class="col-12 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="dosen_id" class="form-label">Dosen</label>
                                <select class="form-select select2" name="dosen_id" id="dosen_id">
                                    <option value="">Pilih Dosen</option>
                                    @foreach ($dosens as $dosen)
                                        <option value="{{ $dosen->id }}"
                                            {{ old('dosen_id', $perkuliahan->dosen_id ?? '') == $dosen->id ? 'selected' : '' }}>
                                            {{ $dosen->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="matkul_id" class="form-label">Mata Kuliah</label>
                                <select class="form-select select2" name="matkul_id" id="matkul_id">
                                    <option value="">Pilih Mata Kuliah</option>
                                    @foreach ($matkuls as $matkul)
                                        <option value="{{ $matkul->id }}"
                                            {{ old('matkul_id', $perkuliahan->matkul_id ?? '') == $matkul->id ? 'selected' : '' }}>
                                            {{ $matkul->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="kelas_id" class="form-label">Kelas</label>
                                <select class="form-select select2" name="kelas_id" id="kelas_id">
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($kelas as $kelas)
                                        <option value="{{ $kelas->id }}"
                                            {{ old('kelas_id', $perkuliahan->kelas_id ?? '') == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->kode }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="semester_id" class="form-label">Semester</label>
                                <select class="form-select select2" name="semester_id" id="semester_id">
                                    <option value="">Pilih Semester</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}"
                                            {{ old('semester_id', $perkuliahan->semester_id ?? '') == $semester->id ? 'selected' : '' }}>
                                            {{ $semester->tahun_ajaran }} - {{ $semester->semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ruangan" class="form-label">Ruangan</label>
                                <input type="text" class="form-control" name="ruangan" id="ruangan"
                                    placeholder="Masukkan Ruangan"
                                    value="{{ old('ruangan', $perkuliahan->ruangan ?? '') }}" />
                            </div>
                            <div class="mb-3">
                                <label for="day" class="form-label">Hari</label>
                                <select class="form-select select2" name="day" id="day">
                                    <option value="">Pilih Hari</option>
                                    <option value="Senin"
                                        {{ old('day', $perkuliahan->day ?? '') == 'Senin' ? 'selected' : '' }}>
                                        Senin
                                    </option>
                                    <option value="Selasa"
                                        {{ old('day', $perkuliahan->day ?? '') == 'Selasa' ? 'selected' : '' }}>
                                        Selasa
                                    </option>
                                    <option value="Rabu"
                                        {{ old('day', $perkuliahan->day ?? '') == 'Rabu' ? 'selected' : '' }}>
                                        Rabu
                                    </option>
                                    <option value="Kamis"
                                        {{ old('day', $perkuliahan->day ?? '') == 'Kamis' ? 'selected' : '' }}>
                                        Kamis
                                    </option>
                                    <option value="Jumat"
                                        {{ old('day', $perkuliahan->day ?? '') == 'Jumat' ? 'selected' : '' }}>
                                        Jumat
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control" name="jam_mulai" id="jam_mulai"
                                    placeholder="Masukkan Jam Mulai"
                                    value="{{ old('jam_mulai', $perkuliahan->jam_mulai ?? '') }}" />
                            </div>
                            <div class="mb-3">
                                <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                <input type="time" class="form-control" name="jam_selesai" id="jam_selesai"
                                    placeholder="Masukkan Jam Selesai"
                                    value="{{ old('jam_selesai', $perkuliahan->jam_selesai ?? '') }}" />
                            </div>

                        </div>
                        <div class="d-flex gap-2">
                            @include('components.form-button', [
                                'route' => route('admin.perkuliahan.index'),
                            ])
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            initSelect2('#dosen_id', {
                placeholder: "Pilih Dosen"
            });
            initSelect2('#matkul_id', {
                placeholder: "Pilih Mata Kuliah"
            });
            initSelect2('#kelas_id', {
                placeholder: "Pilih Kelas"
            });
            initSelect2('#semester_id', {
                placeholder: "Pilih Semester"
            });
        });
    </script>
@endpush
