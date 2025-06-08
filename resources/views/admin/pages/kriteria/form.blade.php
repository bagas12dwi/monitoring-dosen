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
                                    Route::currentRouteName() === 'admin.kriteria.create' ? 'Tambah Data' : 'Edit Data';
                            @endphp
                            {{ $label }} {{ $title }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="form-kriteria"
                    action="{{ isset($kriteria) ? route('admin.kriteria.update', $kriteria->id) : route('admin.kriteria.store') }}"
                    method="post">
                    @csrf
                    @isset($kriteria)
                        @method('PUT')
                    @endisset
                    <div class="row">
                        <div class="col-12 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Kriteria</label>
                                <input type="text" class="form-control" name="nama" id="nama"
                                    placeholder="Masukkan Nama Kriteria" value="{{ old('nama', $kriteria->nama ?? '') }}" />
                            </div>
                            <div class="mb-3">
                                <label for="bobot" class="form-label">Bobot</label>
                                <input type="number" class="form-control" name="bobot" id="bobot" min="1"
                                    max="100" placeholder="Masukkan Bobot"
                                    value="{{ old('bobot', isset($kriteria->bobot) ? $kriteria->bobot * 100 : '') }}" />
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @include('components.form-button', [
                                'route' => route('admin.kriteria.index'),
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
            $('#bobot').on('keyup', function() {
                let value = parseFloat($(this).val());

                if (value > 100) {
                    $(this).val(100);
                } else if (value < 1 && $(this).val() !== '') {
                    $(this).val(1);
                }
            });

            // Optional: Prevent non-numeric characters
            $('#bobot').on('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '');
            });
        });
    </script>
@endpush
