@extends('layouts.template.auth')

@section('title', 'Login')

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
                                    <h6 class="h6 text-gray-900 mb-4">Silahkan login untuk melanjutkan</h6>
                                </div>

                                @error('active')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <form class="user" method="post" action="{{ route('login') }}" id="login_form">
                                    @csrf
                                    <div class="form-group">
                                        <input id="email" type="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Alamat Email">

                                        @error('email')
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
                                    <button type="submit" class="btn btn-info btn-user btn-block mt-4">
                                        Login
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

@push('scripts')
<script>
    $('#login_form').validate({
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
