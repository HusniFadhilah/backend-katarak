@extends('layouts.template.auth')


@php $isVerified = $user->participant()->is_verified ?? false; @endphp
@php $isActive = $user->participant()->is_active ?? false; @endphp

@section('title')
@if (!$isActive)
Akun Anda telah dinonaktifkan
@elseif (!$isVerified)
Menunggu Verifikasi
@endif
@endsection
@section('content')

<div class="login-box">
    <div class="card bg-white shadow">
        {{-- {!! Fungsi::getLogoHeader() !!} --}}
        <div class="card-body">
            <div class="text-left text-muted mb-5">
                <p>
                    @if (!$isVerified)
                    Pendaftaran akun Anda berhasil! Terima kasih telah mendaftar. Sebelum memulai, mohon bersabar menunggu proses verifikasi akun Anda oleh admin
                    @elseif(!$isActive)
                    Mohon maaf, saat ini akun Anda sementara dinonaktifkan oleh admin. Silahkan hubungi admin apabila ada pertanyaan
                    @endif
                </p>
            </div>

            <form method="POST" class="d-inline" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-light my-2">Logout</button>
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
