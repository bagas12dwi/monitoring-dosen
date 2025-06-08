@extends('layouts.app')
@section('title', $title)

@section('content')
    <div class="container-fluid">
        <h3 class="text-dark mb-4">{{ $title }}</h3>
        <div class="card shadow">
            <div class="card-header py-3">
                <div class="row text-sm-start text-center">
                    <div class="col-sm-5 col-12 mb-md-0 mb-3">
                        <p class="text-primary fw-bold m-0 mt-2">Daftar {{ $title }}
                        </p>
                    </div>
                    <div class="col-sm-7 col-12 mb-md-0 mb-2">
                        <div class="d-sm-flex justify-content-sm-end">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label for="nama" class="form-label fw-bold">Update Profil</label>
                        <form action="{{ route('profile.update', $data->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="nim" class="form-label">NIP</label>
                                <input type="text" class="form-control" name="nim" id="nim" placeholder=""
                                    value="{{ old('nim', $data->nim ?? '') }}" disabled />
                            </div>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama" id="nama" placeholder=""
                                    value="{{ old('nama', $data->nama ?? '') }}" />
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label for="name" class="form-label fw-bold">Ganti Password</label>
                        <form action="{{ route('profile.password.update') }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label for="password_old" class="form-label">Password Lama</label>
                                <input type="password" class="form-control" name="password_old" id="password_old"
                                    placeholder="Masukkan Password Lama" />
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" name="password" id="password"
                                    placeholder="Masukkan Password Baru" />
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" name="password_confirmation"
                                    id="confirm_password" placeholder="Masukkan Konfirmasi Password" />
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
