@extends('layouts.frontend.frontend-template')

@section('content')
@if(sizeof($news) > 0)
<div class="container-fluid paddding mb-5">
    <div class="row mx-0 my-5">
        <div class="col-lg-8 col-12">
            <?php foreach($news as $item){ ?>
            <div class="card callout callout-info mx-2">
                <div class="">

                    <p class="text-info">
                        <i class="fa fa-calendar mr-2"></i>
                        {{ \App\Libraries\Date::date_format($item->publish_at) }}
                    </p>
                    <h4 class="my-2 p_proper-screen_xxl">{{ $item->title }}</h4>
                    <p><i class="fa fa-user mr-2">
                        </i>Oleh {{ $item->created_by }}
                    </p>


                    <hr>

                    <img class="card-img-top" src="{{ asset('storage/news/').'/'.$item->banner }}" alt="Card image cap">

                    <hr>
                    <div class="my-3">

                        <div id="html_overview">
                            {!! $item->overview !!}
                        </div>

                        <div class="row mt-4">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ url($item->slug) }}"
                                class="h5 card bg-secondary p-2 p_proper-screen_md mx-1">
                                <h1 class="fa fa-facebook mx-3"></h1>
                            </a>
                            <a href="https://twitter.com/share?url={{ url($item->slug) }}"
                                class="h5 card bg-secondary p-2 p_proper-screen_md mx-1">
                                <h1 class="fa fa-twitter mx-3"></h1>
                            </a>
                            <a href="whatsapp://send?text={{ url($item->slug) }}"
                                class="h5 card bg-secondary p-2 p_proper-screen_md mx-1">
                                <h1 class="fa fa-whatsapp mx-3"></h1>
                            </a>
                        </div>
                    </div>

                </div>
            </div>


            <?php } ?>
        </div>

        <div class="col-lg-4 col-12 mx-0">
            <div class="mx-2 card callout callout-success py-4 px-3">
                <?php foreach($info as $items){ ?>
                <a href="" class="text-black p_proper-screen_xxl">
                    <h6 class="p_proper-screen_xxl">{{ $items->title}}</h6>
                </a>
                <hr>
                <p class="p_proper-screen_md">{{ $items->overview}}</p>

                <?php } ?>

            </div>

            <div class="mx-3">
                <?php foreach($suggest as $items){ ?>
                <hr>
                <a href="{{ route('newsdetail' , $items->slug) }}" class="text-black \">
                    <h6 class="p_proper-screen_xxl">{{ $items->title}}</h6>
                </a>
                <p>Oleh {{ $items->created_by}}</p>

                <?php } ?>

            </div>


        </div>
    </div>
</div>
@else

<div class="container-fluid paddding mb-5 min-vh-100">
    <div class="row m-5 align-self-center">
        <div class="col-12 align-self-center text-center">
            <h2>Ooops,..</h2>
            <p>Berita Tidak Ditemukan...</p>
        </div>
    </div>
</div>

@endif


@endsection