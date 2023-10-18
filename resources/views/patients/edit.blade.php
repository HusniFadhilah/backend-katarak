@extends('layouts.template.admin')

@section('title', 'Edit Pasien')

@php $role = Fungsi::getRoleSession(); @endphp
@section('breadcrumb')
<li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('patient') }}">Pasien</a></li>
<li class="breadcrumb-item text-sm text-dark active" aria-current="page">@yield('title')</li>
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <h1 class="h3 mb-4 text-gray-800">Edit Pasien</h1>
            </div>
            <div class="col-lg-3">
                <small>
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}" class="text-decoration-none text-gray-800 mr-2"><i class="icon-dashboard"></i> Home</a></li>
                        <li><a href="{{ route('patient') }}" class="text-decoration-none text-gray-800 mr-2"><i class="ion-ios-arrow-forward mr-2"></i>Pasien</a></li>
                        <li class="active"><i class="ion-ios-arrow-forward mr-2"></i>Edit</li>
                    </ol>
                </small>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('patient.update',$patient->id) }}" method="post" id="edit_form">
                    @method('patch')
                    @csrf
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Nama *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Nama lengkap" value="{{ old('name',$patient->name) }}" required>
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
                                    <input type="radio" class="form-check-input" id="gender1" name="gender" value="L" {{ request('gender') ?? $patient->gender == 'L' ? 'checked' : '' }} @error('gender') is-invalid @enderror required>
                                    <label class="form-check-label" for="gender1">Laki-laki</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="gender2" name="gender" value="P" {{ request('gender') ?? $patient->gender == 'P' ? 'checked' : '' }} @error('gender') is-invalid @enderror required>
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
                                <input type="password" class="form-control @error('ktp') is-invalid @enderror" name="ktp" placeholder="No KTP" value="{{ old('ktp',$ktp) }}" required>
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
                                <input type="text" class="form-control @error('birth_place') is-invalid @enderror" name="birth_place" placeholder="Tempat lahir" value="{{ old('birth_place',$patient->birth_place) }}" required>
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
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" name="birth_date" placeholder="Tempat lahir" value="{{ old('birth_date',$patient->birth_date) }}" required>
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
                                <textarea rows="4" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Alamat" required>{{ old('address',$patient->address) }}</textarea>
                                @error('address')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-success"><span class="fa fa-save mr-2"></span> Simpan</button>
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
    getDataSelect2('job_id', '{{ route("getjobs") }}', '- Pilih -', {}, false, '{{ $patient->job_id }}', false)

    $('#edit_form').validate({
        rules: {}
        , messages: {}
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
