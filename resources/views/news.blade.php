@extends('layouts.frontend.frontend-template')

@section('content')

<div class="container-fluid paddding mb-5">
    <div class="row mx-0">
        <div class="col-lg-6 col-12 paddding animate-box" data-animate-effect="fadeIn">
            <div>
                <?php for($i = 0; $i < 1; $i++){ ?>

                <img class="img-fluid h-100 w-100" src="{{ asset('storage/news/').'/'.$news[$i]->banner }}" alt="img" />
                <div class="height_position_absolute"></div>
                <div class="absolute_big">
                    <div class=""><i class="fa fa-calendar mr-1"></i>
                        {{ \App\Libraries\Date::date_format($news[$i]->publish_at) }}</a>
                    </div>
                    <div class="">
                        <a href="{{ route('newsdetail' , $news[$i]->slug) }}">
                            <h4 class="text-ellipsis-2 text-white">{{ $news[$i]->title }}</h4>
                        </a>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="row">
                @for($i = 1; $i < sizeof($news); $i++) <div class="col-md-6 col-6 paddding animate-box"
                    data-animate-effect="fadeIn">
                    <div>

                        <img class="img-fluid" src="{{ asset('storage/news/').'/'.$news[$i]->banner }}" alt="img" />
                        <div class="height_position_absolute"></div>
                        <div class="absolute_md">
                            <div class=""><a href="#" class="text-white p_proper-screen_md">
                                    <i class="fa fa-calendar mr-1"></i>
                                    {{ \App\Libraries\Date::date_format($news[$i]->publish_at) }}</a></div>


                            <div class="pX-1 pb-4">
                                <a href="{{ route('newsdetail' , $news[$i]->slug) }}" class="text-white ">
                                    <h6 class="text-ellipsis-2 ">{{ $news[$i]->title }}</h6>
                                </a>
                            </div>
                        </div>
                    </div>
            </div>
            @endfor

        </div>
    </div>

</div>



<div class="container-fluid pb-4 pt-4 paddding">
    <div class="container paddding">
        <div class="row mx-0">
            <div class="col-md-8 animate-box" data-animate-effect="fadeInLeft">
                <div class="ml-2 mt-2 mb-3">
                    <p class="text-warning">Buletin Monalisa</p>
                    <h5><b>Berita Terhangat</b></h5>
                </div>

                @foreach($related_news as $items)
                <div class="row my-4">
                    <div class="col-lg-4 col-4">
                        <img class="img-fluid" src="{{ asset('storage/news/').'/'.$items->banner }}" alt="" />
                    </div>
                    <div class="col-lg-8 col-8 animate-box mt-1">
                        <a href="{{ route('newsdetail' , $items->slug) }}" class="text-black">
                            <h5 class="text-ellipsis-2 p_proper-screen_lg">{{ $items->title }}</h5>
                        </a>
                        <a href="#" class=" p_proper-screen_sm my-1"> <i class="fa fa-user mr-2">
                            </i> {{ $items->created_by }}
                            <small class="px-2">|</small> <i class="fa fa-calendar mr-2"></i>
                            {{ \App\Libraries\Date::date_format($items->publish_at) }}</a>
                        <p class="pt-2 text-ellipsis-3 p_proper-screen_sm">{{ strip_tags($items->overview) }}...
                        </p>
                    </div>
                </div>
                @endforeach


                <div class="row mx-0">
                    <div class="col-12 text-center py-4">
                        <a href="{{ route('newsall', 'hot-news').'?page=1' }}" class="btn btn-dark btn-sm w-100">Lihat
                            Semua</a>
                    </div>
                </div>

            </div>
            <div class="col-md-3 animate-box" data-animate-effect="fadeInRight">
                <div class="ml-2 my-4">
                    <h5><b>Katgori</b></h5>
                </div>
                <div class="clearfix"></div>
                <div class="tags_all mb-4">
                    @foreach($tags as $items)
                    <a href="{{ route('newsall', urlencode($items->name)).'?page=1' }}"
                        class="tagg">{{ $items->name }}</a>
                    @endforeach
                </div>
                <div>
                    <div class="ml-2 mt-2 mb-3">
                        <p class="text-warning">Buletin Monalisa</p>
                        <h5><b>Berita Terbaru</b></h5>
                        <a href="{{ route('newsall', 'live-news').'?page=1' }}" class="w-100">Lihat
                            Semua <i class="fa fa-arrow-right"></i></a>
                    </div>
                </div>
                @foreach($news as $items)
                <div class="row pb-3">
                    <div class="col-lg-6 col-4 align-self-center">
                        <img src="{{ asset('storage/news/').'/'.$items->banner }}" alt="img" class="img-fluid" />
                    </div>
                    <div class="col-lg-6 col-8 paddding">
                        <a href="{{ route('newsdetail' , $items->slug) }}">
                            <p class="text-black text-ellipsis-2 p_proper-screen_lg"> {{ $items->title }}</p>
                        </a>
                        <p class="text-muted p_proper-screen_md"> <i class="fa fa-clock-o mr-1"></i>
                            {{ \App\Libraries\Date::durationDayBetween(Carbon\Carbon::now() , $items->publish_at , 0) }}
                        </p>

                    </div>
                </div>
                @endforeach


            </div>
        </div>


    </div>
</div>

<!-- BERITA TERBARU -->
<div class="container mt-5">
    <div class="ml-2 mt-2 mb-4">
        <p class="text-warning">Buletin Monalisa</p>
        <h5><b>Berita Terpopuler</b></h5>
    </div>

    <div class="row">
        @foreach($hot_news as $items)

        <div class="col-md-3 col-6" style="float:left">
            <div class="card mb-2 p-1">
                <img class="card-img-top" src="{{ asset('storage/news/').'/'.$items->banner }}" alt="Card image cap">
                <div class="card-body">
                    <a class="text-black" href="{{ route('newsdetail' , $items->slug) }}">
                        <h4 class="card-title p_proper-screen_lg text-ellipsis-2">{{ $items->title }}</h4>
                    </a>
                    <br>
                    <p class="p_proper-screen_sm my-2"><i
                            class="fa fa-calendar mr-2"></i><i>{{ \App\Libraries\Date::date_format($items->publish_at) }}</i>
                    </p>
                    <hr>
                    <p class="card-text row p_proper-screen_sm mb-1 text-ellipsis-4">
                        {{ strip_tags($items->overview) }}...</p>

                </div>
            </div>
        </div>

        @endforeach

    </div>

</div>

@endsection