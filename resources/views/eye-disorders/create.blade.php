@extends('layouts.template.admin')

@section('title', 'Tambah Data Keluhan Mata')

@php $role = Fungsi::getRoleSession(); @endphp

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <h1 class="h3 mb-4 text-gray-800">Tambah Data Keluhan Mata</h1>
            </div>
            <div class="col-lg-3">
                <small>
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}" class="text-decoration-none text-gray-800 mr-2"><i class="icon-dashboard"></i> Home</a></li>
                        <li><a href="{{ route('eye-disorder') }}" class="text-decoration-none text-gray-800 mr-2"><i class="ion-ios-arrow-forward mr-2"></i>Data Keluhan Mata</a></li>
                        <li class="active"><i class="ion-ios-arrow-forward mr-2"></i>Tambah</li>
                    </ol>
                </small>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('eye-disorder.store') }}" method="post" id="create_form">
                    @csrf
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Keluhan di Mata *</label>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Keluhan di mata" value="{{ old('name') }}" required>
                                @error('name')
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
