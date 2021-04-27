@extends('layouts.frontend.frontend-template')

@section('content')
<div class="container-login100 ">
    <div class="">

        <form class="card border-warning validate-form px-4 py-2 mt-5 mx-2 bg-white" style="min-width:350px !important"
            action="{{ route('login') }}" method="post">
            @csrf

            <div class="row mt-5" style="align-self: center">
                <div class="col-12">
                    <span class="login100-form-title p-b-30">
                        <img src="{{  asset('assets/img/logo/logo-02.png')  }}" style="height:60px; width:auto;" alt="">
                    </span>
                </div>

            </div>

            @if ($errors->any())
            <p class="text-sm text-red" style="max-width:300px;" class="error" for="first_name">
                {{ $errors->first() }}</p>
            @endif
            <div class="wrap-material-input validate-input" data-validate="Isi Username">
                <input name="username" id="username" class="material-input inputusername p_proper-screen_md"
                    type="text">
                <span class="focus-material-input"></span>
                <span class="label-material-input">Username</span>


            </div>


            <div class="wrap-material-input validate-input" data-validate="Isi Password">
                <input class="material-input inputpassword p_proper-screen_md" type="password" name="password"
                    id="password">
                <span class="focus-material-input"></span>
                <span class="label-material-input">Kata Sandi</span>
            </div>


            <button class="btn btn-outline-info" type="submit">
                Masuk
            </button>

            <div class="flex-sb-m w-full mt-4 p-t-3">
                <div class="d-flex text-center">
                    <p class="p_proper-screen_md">Belum Memiliki Akun?</p>
                    <a href="{{ url('/register') }}" class="ml-1 p_proper-screen_md text-info">
                        Daftar
                    </a>
                </div>
            </div>

            <div class="flex-sb-m w-full p-t-3 p-b-32">
                <div class="d-flex text-center">
                    <p class="p_proper-screen_md">Lupa Kata Sandi?</p>
                    <a href="#" class="ml-1 p_proper-screen_md text-warning">
                        Ingatkan
                    </a>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection