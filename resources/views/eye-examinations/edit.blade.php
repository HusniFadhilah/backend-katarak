@extends('layouts.template.admin')

@section('title', 'Edit Data Pemeriksaan Mata')

@php $role = Fungsi::getRoleSession(); @endphp
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/dropzone/min/dropzone.min.css') }}">
@endpush

@section('breadcrumb')
<li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('eye-examination') }}">Data Pemeriksaan Mata</a></li>
<li class="breadcrumb-item text-sm text-dark active" aria-current="page">@yield('title')</li>
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="h3 mb-4 text-gray-800">Edit Data Pemeriksaan Mata</h1>
            </div>
            <div class="col-lg-4">
                <small>
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}" class="text-decoration-none text-gray-800 mr-2"><i class="icon-dashboard"></i> Home</a></li>
                        <li><a href="{{ route('eye-examination') }}" class="text-decoration-none text-gray-800 mr-2"><i class="ion-ios-arrow-forward mr-2"></i>Data Pemeriksaan Mata</a></li>
                        <li class="active"><i class="ion-ios-arrow-forward mr-2"></i>Edit</li>
                    </ol>
                </small>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('eye-examination.update',$eyeExamination->id) }}" method="post" id="edit_form">
                    @method('patch')
                    @csrf
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Pasien *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <select name="patient_id" id="patient_id" class="form-control select2bs4 @error('patient_id') is-invalid @enderror" required>
                                    <option value="">- Pilih -</option>
                                </select>
                                @error('patient_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Kader *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <select name="kader_id" id="kader_id" class="form-control select2bs4 @error('kader_id') is-invalid @enderror" required>
                                    <option value="">- Pilih -</option>
                                </select>
                                @error('kader_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Dokter *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <select name="doctor_id" id="doctor_id" class="form-control select2bs4 @error('doctor_id') is-invalid @enderror" required>
                                    <option value="">- Pilih -</option>
                                </select>
                                @error('doctor_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Tanggal pemeriksaan *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="date" class="form-control @error('examination_date_time') is-invalid @enderror" name="examination_date_time" placeholder="Tanggal pemeriksaan" value="{{ old('examination_date_time',\Carbon\Carbon::parse($eyeExamination->examination_date_time)->format('Y-m-d')) }}" required>
                                @error('examination_date_time')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Hasil Pemeriksaan Mata Kanan (1-8) *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="number" min="1" max="8" class="form-control @error('right_eye_vision') is-invalid @enderror" name="right_eye_vision" placeholder="Hasil pemeriksaan mata kanan" value="{{ old('right_eye_vision',$eyeExamination->right_eye_vision) }}" required>
                                @error('right_eye_vision')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Hasil Pemeriksaan Mata Kiri (1-8) *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="number" min="1" max="8" class="form-control @error('left_eye_vision') is-invalid @enderror" name="left_eye_vision" placeholder="Hasil pemeriksaan mata kiri" value="{{ old('left_eye_vision',$eyeExamination->left_eye_vision) }}" required>
                                @error('left_eye_vision')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Keluhan di Mata *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <select name="eye_disorders_id[]" id="eye_disorders_id" class="form-control select2 @error('eye_disorders_id') is-invalid @enderror" required multiple>
                                    <option value="">- Pilih -</option>
                                </select>
                                @error('eye_disorders_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Riwayat Penyakit Dahulu *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <select name="past_medicals_id[]" id="past_medicals_id" class="form-control select2 @error('past_medicals_id') is-invalid @enderror" required multiple>
                                    <option value="">- Pilih -</option>
                                </select>
                                @error('past_medicals_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Foto Mata *</label>
                        </div>
                        <div class="col-lg-4">
                            <img src="{{ asset($eyeImagesPath ? $eyeImagesPath[0]:'assets/img/default.jpg') }}" data-src="{{ asset($eyeImagesPath ? $eyeImagesPath[0]:'assets/img/default.jpg') }}" class="img-thumbnail" id="img-preview">
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-file @error('image') is-invalid @enderror">
                                    <div class="input-group">
                                        <input type="file" id="image" name="image" accept="image/*" onchange="previewImg('image','img-preview')" class="form-control @error('image') is-invalid @enderror" />
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2" style="cursor:pointer"><i class="fa fa-times" onclick="clearUpload()"></i></span>
                                        </div>
                                    </div>
                                </div>
                                @error('image')
                                <span class="text-warning" role="alert">
                                    <small><strong>{{ $message }}</strong></small>
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
<script src="{{ asset('assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
<script>
    getDataSelect2('patient_id', '{{ route("getpatients") }}', '- Pilih -', {}, false, '{{ $eyeExamination->patient_id }}', false)
    getDataSelect2('kader_id', '{{ route("getkaders") }}', '- Pilih -', {}, false, '{{ $eyeExamination->kader_id }}', false)
    getDataSelect2('doctor_id', '{{ route("getdoctors") }}', '- Pilih -', {}, false, '{{ $eyeExamination->doctor_id }}', false)
    getDataSelect2('past_medicals_id', '{{ route("getpastmedicals") }}', '- Pilih -', {}, false, JSON.parse('{{ json_encode($pastMedicalIds) }}'), false)
    getDataSelect2('eye_disorders_id', '{{ route("geteyedisorders") }}', '- Pilih -', {}, false, JSON.parse('{{ json_encode($eyeDisorderIds) }}'), false)

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
