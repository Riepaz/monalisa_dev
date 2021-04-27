@extends('layouts.frontend.frontend-template')

@section('content')


<div class="container-fluid pb-4 pt-3 paddding">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-default">

        <button class="btn btn-sm btn-outline-light d-inline-block d-md-none mobile-nav ml-auto" type="button"
            data-toggle="collapse" data-target="#navbarCategoryContent" aria-controls="navbarCategoryContent"
            aria-expanded="false" aria-label="Toggle navigation">
            Kategori
        </button>

        <div class="collapse navbar-collapse mt-5 my-lg-0" id="navbarCategoryContent">
            <ul class="navbar-nav m-auto">
                <li class="nav-item {{ request()->is('infoall/all-info') ? 'active' : '' }} mr-3">
                    <a class="nav-link" href="{{ route('infoall', 'all-info').'?page=1' }}">Semua<span
                            class=" sr-only">(current)</span></a>
                </li>
                <li class="nav-item mr-3">
                    <p class="nav-link">|</p>
                </li>

                @foreach($tags as $items)
                <li class="nav-item {{ request()->is('infoall/'.urlencode($items->name)) ? 'active' : '' }}">
                    <a class="nav-link"
                        href="{{ route('infoall', urlencode($items->name)).'?page=1'.(isset($_GET['province_id']) ? '&province_id='.$_GET['province_id'] : '') }}">{{ $items->name }}<span
                            class="sr-only">(current)</span></a>
                </li>
                @endforeach

                <li class="nav-item dropdown">
                    <a id="dropdowntogglefilter" class="nav-link dropdown-toggle" href="#" id="navbarDropdown"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-filter"></i>
                    </a>
                    <div id="dropdowncontainerfilter" class="dropdown-menu px-2 py-4" aria-labelledby="navbarDropdown">
                        <form action="" mehtod="GET" enctype="multipart/form-data">

                            <input name="page" id="page" value="{{ $_GET['page'] }}" readonly hidden />
                            <select name="province_id" id="province_id" class="form-control form-control-sm"></select>

                            <button class="btn btn-primary btn-sm mt-2 w-100">Filter</button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    @if(sizeof($info) > 0)
    <div class="container paddding my-5 h-100 ">
        <div class="row mx-2">
            <div class="col-lg-9 col-12 animate-box" data-animate-effect="fadeInLeft">
                <div class="ml-2 mt-2 mb-3">
                    <p class="text-warning">Info Monalisa</p>
                    <h5><b>{{ $title }}</b></h5>
                </div>

                @foreach($info as $items)

                <?php 
                    $color='danger';
                    if($items->category_id == 1){
                        $color = 'success';
                    } else if($items->category_id == 2){
                        $color = 'info';
                    }
                ?>

                <div class="card card-outline card-{{ $color }} my-4 mx-2">
                    <div class="row p-4">
                        <div class="col-lg-12 col-12 animate-box mt-1">
                            <h5>{{ $items->title }}</h5>
                            <p class="p_proper-screen_sm"><i class="fa fa-user mr-2">
                                </i> {{ $items->created_by }}
                                <small class="px-2">|</small> <i class="fa fa-calendar mr-2"></i>
                                {{ \App\Libraries\Date::date_format($items->publish_at) }}
                            </p>
                            <hr>
                            <p class="overview p_proper-screen_sm">{{ strip_tags($items->overview) }}...
                            </p>
                        </div>
                    </div>

                </div>
                @endforeach


                <div class="row m-3">
                    {{ $info->links() }}
                </div>

            </div>
        </div>
        @else

        <div class="container-fluid paddding mb-5 min-vh-100">
            <div class="row m-5 align-self-center">
                <div class="col-12 align-self-center text-center">
                    <h2>Yah...!!</h2>
                    <p>Informasi Tidak Ditersedia...</p>
                </div>
            </div>
        </div>

    </div>
    @endif
    @endsection



    @section('javascript')
    <script type="text/javascript">
    $.ajax({
        url: "{{ route('provinces.get') }}",
        type: "GET",
        dataType: "json",
        success: function(data) {
            $('select[name="province_id"]').empty();
            $.each(data, function(key, value) {
                $('select[name="province_id"]').append('<option value="' + key + '">' +
                    value + '</option>');
            });
            if ("{{ $_GET['province_id'] ?? '' }}") {
                $('select[name="province_id"]').val("{{ $_GET['province_id'] ?? '' }}");
            }
        }
    });

    $(function() {

        $('#dropdowntogglefilter').on('click', function(event) {
            $('#dropdowncontainerfilter').slideToggle();
            event.stopPropagation();
        });

        $('#dropdowncontainerfilter').on('click', function(event) {
            event.stopPropagation();
        });

        $(window).on('click', function() {
            $('#dropdowncontainerfilter').slideUp();
        });

    });
    </script>
    @endsection