@extends('layouts.template.auth')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="container pt-5">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-lg-5">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg col-sm col-md">
                            <div class="p-lg-5 p-4 p-md-5">
                                <div class="text-center">
                                    <a href="{{ route('home') }}" class="nav-link sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                                        <div class="sidebar-brand-icon">
                                            <img class="img-fluid img65" src="{{ asset('assets/img/logo/logo-long.png') }}" alt="Logo">
                                        </div>
                                        {{-- <h5 class="sidebar-brand-text mx-3 text-left text-uppercase">Deteksi Dini Katarak</h5> --}}
                                    </a>
                                </div>
                                <br />
                                <div class="text-center">
                                    <h6 class="h6 text-gray-900 mb-4">Daftar akun baru</h6>
                                </div>

                                @error('active')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <form class="user" method="post" action="{{ route('register') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input id="name" type="text" class="form-control form-control-user @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Nama lengkap">

                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input id="email" type="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Alamat email">

                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input id="phone_number" type="text" class="form-control form-control-user @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number" autofocus placeholder="No HP">

                                        @error('phone_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input id="password" type="password" class="form-control form-control-user @error('password') is-invalid @enderror" name="password" required placeholder="Password">

                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input id="password_confirmation" type="password" class="form-control form-control-user @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required placeholder="Konfirmasi password">

                                        @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control @error('role_id') is-invalid @enderror" id="role_id" name="role_id" placeholder="- Pilih Role -" style="font-size: .8rem;border-radius: 10rem;padding-top:0.5rem;padding-bottom:0.5rem;height: calc(3rem);">
                                            <option value="">- Pilih Role -</option>
                                            <option value="2" {{ old('role_id') == '2' ? 'selected' : '' }}>Kader</option>
                                            <option value="3" {{ old('role_id') == '3' ? 'selected' : '' }}>Dokter</option>
                                        </select>
                                        @error('role_id')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-info btn-user btn-block mt-4">
                                        Daftar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center bg-white">
                    <p class="text-sm mx-auto mb-0">
                        Lupa password?
                        <a href="{{ route('password.request') }}" class="text-success font-weight-bold">Reset di sini</a>
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
