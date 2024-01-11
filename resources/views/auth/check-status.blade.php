@extends('layouts.template.auth')


@php $isVerified = $user->is_verified ?? false; @endphp
@php $isActive = $user->is_active ?? false; @endphp

@section('title')
@if (!$isActive)
Akun Anda telah dinonaktifkan
@elseif (!$isVerified)
Menunggu Verifikasi
@endif
@endsection
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
                                    <h6 class="h6 text-gray-900 mb-4">
                                        @if (!$isVerified)
                                        Pendaftaran akun {{ $user->role->alias }} berhasil! Terima kasih telah mendaftar. Sebelum memulai, mohon bersabar menunggu proses verifikasi akun Anda oleh admin
                                        @elseif(!$isActive)
                                        Mohon maaf, saat ini akun Anda sementara dinonaktifkan oleh admin. Silahkan hubungi admin apabila ada pertanyaan
                                        @endif</h6>
                                </div>

                                @error('active')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <form method="POST" class="d-inline" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-info btn-user btn-block mt-4">
                                        Logout
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
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-primary font-weight-bold">Daftar di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
