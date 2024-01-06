@extends('layouts.template.auth')

@section('title', 'Reset Password')

@section('content')
<div class="container mt-5">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-lg-5 mt-5">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg col-sm col-md p-4">
                            <div class="p-lg-4 p-4 p-md-4">
                                <div class="text-center">
                                    <a href="{{ route('home') }}" class="nav-link sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                                        <div class="sidebar-brand-icon">
                                            <img class="img-fluid img65" src="{{ asset('assets/img/logo/logo-long.png') }}" alt="Logo">
                                        </div>
                                        {{-- <h5 class="sidebar-brand-text mx-3 text-left text-uppercase">Deteksi Dini Katarak</h5> --}}
                                    </a>
                                </div>
                                <p class="text-justify my-3">Masukkan password baru akun Anda</p>
                                @error('active')
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    {{ $message }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @enderror
                                <form role="form" class="user" method="post" action="{{ route('password.update') }}" id="reset-password-form">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                    <div class="form-group">
                                        <input id="email" type="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email',$_GET['email']) }}" required @if($_GET['email'] !='' ) readonly @endif autocomplete="email" autofocus placeholder="Email">
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input id="password" type="password" class="form-control form-control-user @error('password') is-invalid @enderror" name="password" placeholder="Password baru">
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input id="password_confirmation" type="password" class="form-control form-control-user @error('password_confirmation') is-invalid @enderror" name="password_confirmation" placeholder="Konfirmasi password baru">
                                        @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success btn-user btn-block">
                                            Reset Password Sekarang
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <p class="text-sm mx-auto mb-0">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-success font-weight-bold">Daftar di sini</a>
                    </p>
                    <p class="text-sm mx-auto mb-0">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-primary font-weight-bold">Masuk di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
