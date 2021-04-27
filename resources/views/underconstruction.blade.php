@extends('layouts.frontend.frontend-template')

@section('content')
<div class="vh-100">

    <div class="jumbotron under-construct-bg">
        <h1 class="display-1 mt-5"><img class="header-bg" src="{{ url('assets/img/logo/welcome_logo-01.png') }}" alt="">
        </h1>
        <p class="p_proper-screen_sm">Pusat Pendidikan dan Pelatihan Kependudukan dan Keluarga Berencana.</p>
        <p class="p_proper-screen_md">Belum Melakukan Pendaftaran? <b>Daftar Sekarang</b></p>

        <p class="lead mt-2">
            <a class="btn btn-outline-info btn-sm" href="{{ url('signin') }}" role="button">Masuk Sebagai
                Peserta</a>
        </p>
    </div>
    <div class="px-5 py-2">
        <hr>
    </div>
</div>

@endsection