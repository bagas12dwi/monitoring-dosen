@extends('layouts.guess')

@section('login')
    <div class="container">
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="toast" class="toast align-items-center border-0 text-white" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"></div>
                    <button type="button" class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        <div class="row justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-md-12 d-flex justify-content-center">
                <div>
                    <h4 class="text-center text-white m-0">Sistem Penilaian Dosen</h4>
                    <div class="card o-hidden border-0 shadow-lg mt-4" style="width: 40em;">
                        <div class="card-body p-0">
                            <div class="p-5">
                                <h4 class="text-dark mb-4 fw-bold">Silahkan Login</h4>
                                <form action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <div class="form-floating mb-3">
                                        <input class="form-control @error('nim') is-invalid @enderror" type="nim"
                                            id="text" aria-describedby="nim" placeholder="Username" name="nim"
                                            value="{{ old('nim') }}" required autofocus>
                                        <label for="nik">Username</label>
                                        @error('nik')
                                            <span class="invalid-feedback" role="alert">
                                                <p>{{ $message }}</p>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input class="form-control" type="password" id="password" placeholder="Password"
                                            name="password" required autocomplete="current-password">
                                        <label for="password">Password</label>
                                    </div>
                                    {{-- <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="custom-control custom-checkbox small">
                                                <div class="form-check">
                                                    <input class="form-check-input custom-control-input" type="checkbox"
                                                        id="remember_me">
                                                    <label class="form-check-label custom-control-label" for="remember_me">
                                                        Remember Me
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <a onclick="forgotPassword()" class="small" href="#">Forgot
                                                    Password?</a>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <button class="btn btn-primary d-block btn-user w-100" type="submit">Login</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @includeIf('components.toast')

        <script src="{{ URL::asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery-3.6.3.min.js') }}"></script>
        <script>
            function forgotPassword() {
                $('#toast').addClass('text-bg-success')
                    .removeClass('text-bg-danger');
                $('#toast').toast('show');
                $('#toast .toast-body').text(
                    'Silahkan hubungi admin untuk mereset password!');
            }
        </script>
    @endsection
