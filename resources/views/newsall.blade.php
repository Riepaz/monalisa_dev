@extends('layouts.frontend.frontend-template')

@section('content')


<div class="container-fluid pb-4 pt-3 paddding">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-default">

        <button class="btn btn-sm btn-outline-light d-inline-block d-md-none mobile-nav ml-auto my-2" type="button"
            data-toggle="collapse" data-target="#navbarCategoryContent" aria-controls="navbarCategoryContent"
            aria-expanded="false" aria-label="Toggle navigation">
            Kategori
        </button>

        <div class="collapse navbar-collapse mt-5 my-lg-0" id="navbarCategoryContent">
            <ul class="navbar-nav m-auto">
                @foreach($tags as $items)
                <li class="nav-item {{ request()->is('newsall/'.urlencode($items->name)) ? 'active' : '' }}">
                    <a class="nav-link"
                        href="{{ route('newsall', urlencode($items->name)).'?page=1' }}">{{ $items->name }}<span
                            class="sr-only">(current)</span></a>
                </li>
                @endforeach
            </ul>
        </div>
    </nav>

    @if(sizeof($news) > 0)
    <div class="container paddding my-5 h-100 ">
        <div class="row mx-2">

            <div class="col-lg-9 col-12 animate-box" data-animate-effect="fadeInLeft">
                <div class="ml-2 mt-2 mb-3">
                    <p class="text-warning">Buletin Monalisa</p>
                    <h5><b>{{ $title }}</b></h5>
                </div>

                @foreach($news as $items)
                <div class="row my-4">
                    <div class="col-lg-4 col-4">
                        <img class="img-fluid" src="{{ asset('storage/news/').'/'.$items->banner }}" alt="" />
                    </div>
                    <div class="col-lg-8 col-8 animate-box mt-1">
                        <a href="{{ route('newsdetail' , $items->slug) }}" class="text-black">
                            <h5 class="text-ellipsis-2 p_proper-screen_lg">{{ $items->title }}</h5>
                        </a>
                        <a href="{{ route('newsdetail' , $items->slug) }}" class=" p_proper-screen_sm my-1"> <i
                                class="fa fa-user mr-2">
                            </i> {{ $items->created_by }}
                            <small class="px-2">|</small> <i class="fa fa-calendar mr-2"></i>
                            {{ \App\Libraries\Date::date_format($items->publish_at) }}</a>
                        <p class="pt-2 text-ellipsis-3 p_proper-screen_sm">{{ strip_tags($items->overview) }}...
                        </p>
                    </div>
                </div>
                @endforeach


                <div class="row m-3">
                    {{ $news->links() }}
                </div>

            </div>
        </div>


    </div>
    @else

    <div class="container-fluid paddding mb-5 min-vh-100">
        <div class="row m-5 align-self-center">
            <div class="col-12 align-self-center text-center">
                <h2>Woopss...!!</h2>
                <p>Berita Tidak Ditersedia pada Kategori ini...</p>
            </div>
        </div>
    </div>


    @endif

</div>
@endsection