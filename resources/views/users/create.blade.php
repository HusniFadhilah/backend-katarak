@extends('layouts.template.admin')

@section('title', 'Tambah User')

@php $role = Fungsi::getRoleSession(); @endphp

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <h1 class="h3 mb-4 text-gray-800">Tambah User</h1>
            </div>
            <div class="col-lg-3">
                <small>
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}" class="text-decoration-none text-gray-800 mr-2"><i class="icon-dashboard"></i> Home</a></li>
                        <li><a href="{{ route('user') }}" class="text-decoration-none text-gray-800 mr-2"><i class="ion-ios-arrow-forward mr-2"></i>User</a></li>
                        <li class="active"><i class="ion-ios-arrow-forward mr-2"></i>Tambah</li>
                    </ol>
                </small>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('user.store') }}" method="post" id="create_form">
                    @csrf
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Nama *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Nama lengkap" value="{{ old('name') }}" required>
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
                            <label>Email *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}" required>
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
                            <label>Password *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" value="{{ old('password') }}" required>
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
                            <label>Konfirmasi Password *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" placeholder="Konfirmasi Password" value="{{ old('password_confirmation') }}" required>
                                @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Role *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <select name="role_id" id="role_id" class="form-control select2bs4 @error('role_id') is-invalid @enderror" required>
                                    <option value="">- Pilih -</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->alias }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-success"><span class="fa fa-plus mr-2"></span> Tambah</button>
                        <button type="reset" class="btn btn-light"><span class="fa fa-redo-alt mr-2"></span> Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $('#create_form').validate({
        rules: {
            password: {
                minlength: 8
                , maxlength: 30
                , required: true
            }
            , password_confirmation: {
                equalTo: "#password"
            }
        }
        , messages: {
            password: {
                minlength: "Isian password harus minimal 8 karakter"
                , maxlength: "Isian password seharusnya tidak lebih dari 30 karakter"
                , required: "Bidang isian password wajib diisi"
            }
            , password_confirmation: {
                equalTo: "Konfirmasi password tidak cocok"
            }
        }
        , errorElement: 'span'
        , errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        }
        , highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        }
        , unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });

</script>
@endpush
