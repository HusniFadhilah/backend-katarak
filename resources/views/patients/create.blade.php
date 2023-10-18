@extends('layouts.template.admin')

@section('title', 'Tambah Pasien')

@php $role = Fungsi::getRoleSession(); @endphp

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <h1 class="h3 mb-4 text-gray-800">Tambah Pasien</h1>
            </div>
            <div class="col-lg-3">
                <small>
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}" class="text-decoration-none text-gray-800 mr-2"><i class="icon-dashboard"></i> Home</a></li>
                        <li><a href="{{ route('patient') }}" class="text-decoration-none text-gray-800 mr-2"><i class="ion-ios-arrow-forward mr-2"></i>Pasien</a></li>
                        <li class="active"><i class="ion-ios-arrow-forward mr-2"></i>Tambah</li>
                    </ol>
                </small>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('patient.store') }}" method="post" id="create_form">
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
                            <label>Jenis Kelamin *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="gender1" name="gender" value="L" {{ old('gender') === 'L' ? 'checked' : '' }} @error('gender') is-invalid @enderror required>
                                    <label class="form-check-label" for="gender1">Laki-laki</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="gender2" name="gender" value="P" {{ old('gender') === 'P' ? 'checked' : '' }} @error('gender') is-invalid @enderror required>
                                    <label class="form-check-label" for="gender2">Perempuan</label>
                                </div>
                                @error('gender')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>No KTP *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="password" class="form-control @error('ktp') is-invalid @enderror" name="ktp" placeholder="No KTP" value="{{ old('ktp') }}" required>
                                @error('ktp')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Tempat Lahir *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="text" class="form-control @error('birth_place') is-invalid @enderror" name="birth_place" placeholder="Tempat lahir" value="{{ old('birth_place') }}" required>
                                @error('birth_place')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Tanggal Lahir *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" name="birth_date" placeholder="Tempat lahir" value="{{ old('birth_date') }}" required>
                                @error('birth_date')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Pekerjaan *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <select name="job_id" id="job_id" class="form-control select2bs4 @error('job_id') is-invalid @enderror" required>
                                    <option value="">- Pilih -</option>
                                </select>
                                @error('job_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Alamat *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <textarea rows="4" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Alamat" required>{{ old('address') }}</textarea>
                                @error('address')
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
    getDataSelect2Search('job_id', '{{ route("getjobs") }}', '- Pilih -', 0, false, {}, true)

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
