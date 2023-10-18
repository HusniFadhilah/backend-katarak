@extends('layouts.template.admin')

@section('title', 'Edit Profile')

@php $role = Fungsi::getRoleSession(); @endphp

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <h1 class="h3 mb-4 text-gray-800">Edit Profile</h1>
            </div>
            <div class="col-lg-3">
                <small>
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}" class="text-decoration-none text-gray-800 mr-2"><i class="icon-dashboard"></i> Home</a></li>
                        <li class="active"><i class="ion-ios-arrow-forward mr-2"></i>Profile</li>
                    </ol>
                </small>
            </div>
        </div>
        @if (count($errors) > 0)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Error!</h5>
                    <ol class="pl-3">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-lg">
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('profile.update', $user->id) }}" method="post">
                            @csrf
                            @method('patch')
                            <div class="row">
                                <div class="col-lg-2">
                                    <label>Nama *</label>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Nama lengkap" value="{{ old('name', $user->name) }}">
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <label>Username *</label>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" placeholder="Username" value="{{ old('username', $user->username) }}" readonly>
                                        @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <label>Email *</label>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email', $user->email) }}">
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <label>Password</label>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}">
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <label>Konfirmasi Password</label>
                                </div>
                                <div class="col-lg-10">
                                    <div class="form-group">
                                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" value="{{ old('password_confirmation') }}">
                                        @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5">
                                <button type="submit" class="btn btn-success"><span class="fa fa-save mr-2"></span> Simpan</button>
                                <a href="{{ route('dashboard') }}" id="back" class="btn btn-light"><span class="fa fa-times mr-2"></span> Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
