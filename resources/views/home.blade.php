@extends('layouts.frontend.frontend-template')

@section('content')
<div class="jumbotron jumbotron-bg">
    <h1 class="display-1 mt-5"><img class="header-bg" src="{{ url('assets/img/logo/welcome_logo-01.png') }}" alt="">
    </h1>
    <hr class="w-50 d-lg-block d-none">
    <p class="p_proper-screen_sm w-75">Lini Lapangan Badan Kependudukan dan Keluarga Berencana.</p>
    <p class="p_proper-screen_sm w-75">Dapatkan segala informasi tentang Analisa dan Mekanisme Operasional disini.</p>

    </p>

</div>


<div class="container mb-5">
    @if(sizeof($info) > 0)
    <!-- BERITA TERBARU -->
    <div class="container mt-5">
        <div class="ml-auto mt-2 mb-4 text-right">
            <p class="text-info">Info Monalisa</p>
            <h5><b>Informasi Khusus</b></h5>
            <a href="{{ route('infoall', 'all-info').'?page=1' }}" class="w-100">Lihat
                Semua <i class="fa fa-arrow-right"></i></a>
        </div>

        <div class="row ml-1">
            <?php foreach($info as $items){ ?>

            <div class="col-md-3 col-6" style="float:left">
                <?php 
                    $color='danger';
                    if($items->category_id == 1){
                        $color = 'success';
                    } else if($items->category_id == 2){
                        $color = 'info';
                    }
                ?>
                <div class="card card-outline card-{{ $color }} mb-2 p-1">
                    <div class="card-body">

                        <h4 class="card-title p_proper-screen_lg text-ellipsis-3 w-100">{{ $items->title }}</h4>

                        <br>
                        <p class="p_proper-screen_sm w-100">
                            <i class="fa fa-clock-o mr-1"></i>
                            {{ \App\Libraries\Date::durationDayBetween(Carbon\Carbon::now() , $items->publish_at , 0) }}
                        </p>
                        <hr>
                        <p class="card-text row p_proper-screen_sm mb-1 text-ellipsis-4">
                            {{ strip_tags($items->overview) }}...</p>

                    </div>
                </div>
            </div>

            <?php } ?>

        </div>
    </div>
    @endif

    <!-- BERITA TERBARU -->
    <div class="container mt-5">
        <div class="ml-auto mt-2 mb-4 text-right">
            <p class="text-info">Buletin Monalisa</p>
            <h5><b>Berita Terbaru</b></h5>
            <a href="{{ route('news') }}" class="w-100">Lihat
                Semua <i class="fa fa-arrow-right"></i></a>
        </div>

        <div class="row ml-1">
            <?php foreach($news as $items){ ?>

            <div class="col-md-3 col-6" style="float:left">
                <div class="card mb-2 p-1">
                    <img class="card-img-top" src="{{ asset('storage/news/').'/'.$items->banner }}"
                        alt="Card image cap">
                    <div class="card-body">
                        <a class="text-black" href="{{ route('newsdetail' , $items->slug) }}">
                            <h4 class="card-title p_proper-screen_lg text-ellipsis-3">{{ $items->title }}</h4>
                        </a>
                        <br>
                        <p class="p_proper-screen_sm"><i
                                class="fa fa-calendar mr-2"></i><i>{{ \App\Libraries\Date::date_format($items->publish_at) }}</i>
                        </p>
                        <hr>
                        <p class="card-text row p_proper-screen_sm mb-1 text-ellipsis-4">
                            {{ strip_tags($items->overview) }}...</p>

                    </div>
                </div>
            </div>

            <?php } ?>

        </div>

    </div>
</div>



@endsection