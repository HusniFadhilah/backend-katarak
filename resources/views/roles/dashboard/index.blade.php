@extends('layouts.template.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="halo">
        <hr>
        <h4>Halo <strong>{{ $authUser->name }}</strong></h4>
        Selamat Datang di <b>Dashboard Admin Web Deteksi Dini Katarak</b><br>
        Role Anda adalah <b>{{ $authUser->role->name }}</b>
        <hr>
    </div>
    <!-- Content Row -->
    <div class="row">
        @foreach ($countData as $data)
        <a class="col-xl-3 col-md-6 mb-4 btn-nero text-decoration-none" href="{{ $data['href'] }}">
            <div class="card bg-{{ $data['bg'] }} shadow h-100 py-2 home">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h6 font-weight-bold text-light text-uppercase mb-1">{{ $data['title'] }}</div>
                            <div class="h3 mb-0 font-weight-bold text-light">{{ $data['count'] }}</div>
                        </div>
                        <div class="col-auto homes">
                            <i class="{{ $data['icon'] }} fa-4x text-light"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <!-- End of Row -->
</div>
@endsection
