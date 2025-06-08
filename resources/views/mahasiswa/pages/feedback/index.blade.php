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
    <style>
        .form-check-inline .form-check-input {
            margin-right: 0.5rem;
        }

        .scale-label {
            font-size: 0.875rem;
            text-align: center;
            width: 100%;
        }
    </style>
    <div class="container-fluid">
        <h3 class="text-dark mb-4">{{ $title }}</h3>
        <div class="card shadow">
            <div class="card-header py-3">
                <div class="row text-sm-start text-center">
                    <div class="col-sm-5 col-12 mb-md-0 mb-3">
                        <p class="text-primary fw-bold m-0 mt-2">Daftar {{ $title }}
                        </p>
                        {{-- @include('components.modal-import', [
                            'title' => 'Penilaian',
                            'route' => route('mahasiswa.feedback.import'),
                        ]) --}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse ($data as $dosen)
                        @foreach ($dosen->perkuliahan as $perkuliahan)
                            <div class="col-4 col-md-4 col-sm-12">
                                <div class="card mb-3">
                                    <div class="card-body ">
                                        <div class="d-flex align-items-center gap-2 mb-4">
                                            <img class="rounded-circle img-profile border"
                                                style="object-fit: cover; height: 3em; width: 3em" alt="Avatar"
                                                src="{{ URL::asset('assets/img/default.jpg') }}">
                                            <div class="data-dosen">
                                                <h5 class="card-title">{{ $dosen->nama }}</h5>
                                                <p class="m-0">NIP. {{ $dosen->nim }}</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <p>{{ $perkuliahan->matkul->nama }}</p>
                                        <a href="#" class="btn btn-primary btn-penilaian btn-icon-split"
                                            data-id="{{ $perkuliahan->id }}">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-plus"></i>
                                            </span>
                                            <span class="text">Isi Penilaian</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <h3 class="text-center">Belum Memilih Matkul / Anda sudah mengisi penilaian</h3>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="penilaianModal" tabindex="-1" aria-labelledby="penilaianModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('mahasiswa.feedback.store') }}">
                @csrf
                <input type="hidden" name="perkuliahan_id" id="modal_perkuliahan_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="penilaianModalLabel">Isi Penilaian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @foreach ($pertanyaans as $index => $pertanyaan)
                            <div class="mb-3">
                                <label class="form-label d-block fw-bold">{{ $loop->iteration }}.
                                    {{ $pertanyaan->pertanyaan }}</label>
                                <input type="hidden" name="pertanyaan_id[]" value="{{ $pertanyaan->id }}">

                                <table style="width: 100%">
                                    <thead>
                                        <th class="text-center" style="width: 25%">Sangat Tidak Setuju</th>
                                        <th class="text-center">1</th>
                                        <th class="text-center">2</th>
                                        <th class="text-center">3</th>
                                        <th class="text-center">4</th>
                                        <th class="text-center">5</th>
                                        <th class="text-center" style="width: 25%">Sangat Setuju</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"></td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio"
                                                    name="nilai[{{ $index }}]" id="q{{ $pertanyaan->id }}_1"
                                                    value="1" required>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio"
                                                    name="nilai[{{ $index }}]" id="q{{ $pertanyaan->id }}_2"
                                                    value="2" required>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio"
                                                    name="nilai[{{ $index }}]" id="q{{ $pertanyaan->id }}_3"
                                                    value="3" required>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio"
                                                    name="nilai[{{ $index }}]" id="q{{ $pertanyaan->id }}_4"
                                                    value="4" required>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio"
                                                    name="nilai[{{ $index }}]" id="q{{ $pertanyaan->id }}_5"
                                                    value="5" required>
                                            </td>
                                            <td class="text-center"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                        <div class="mb-3">
                            <label for="komentar" class="form-label fw-bold">Catatan Tambahan</label>
                            <textarea class="form-control" name="komentar" id="komentar" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Kirim Penilaian</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.btn-penilaian').on('click', function(e) {
                e.preventDefault();

                var perkuliahanId = $(this).data('id');
                $('#modal_perkuliahan_id').val(perkuliahanId);

                $('#penilaianModal').modal('show');
            });
        });
    </script>
@endpush
