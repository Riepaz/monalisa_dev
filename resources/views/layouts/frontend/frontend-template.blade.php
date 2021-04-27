<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="vh-100">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="expires" content="<?php echo date('r');?>" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta http-equiv="content-language" content="id" />
    <meta name="author" content="Lini Lapangan BKKBN" />
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
    <meta name="keywords"
        content="monalisa, monitoring dan analisa , mekop , bkkbn, administrasi, bkkbn provinsi, indonesia, mendagri, menpanrb" />

    <meta name="title" content="Monalisa | BkkbN" />
    <meta name="description"
        content="Berencana itu Keren, ketahui informasi tempat tinggalmu dalam perkembangan program keluarga berencana di sini." />

    <meta name="robots" content="index, follow" />
    <title>Monalisa</title>
    <link rel="icon" href="{{  asset('assets/img/logo/bkkbn-big.png')  }}" type="image/png">

    <link rel="stylesheet" href="{{  asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.css')  }}">
    <link rel="stylesheet" href="{{  asset('assets/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')  }}">
    <link rel="stylesheet" href="{{  asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')  }}">
    <link rel="stylesheet" href="{{  asset('assets/dist/css/adminlte.min.css')  }}">

    @if(request()->is('login'))
    <link rel="stylesheet" href="{{  asset('assets/css/login-style.css')  }}">
    @endif

    <link rel="stylesheet" href="{{  asset('assets/css/main.css')  }}">
    <link rel="stylesheet" href="{{  asset('assets/css/util.css')  }}">

    <link rel="stylesheet" href="{{  asset('assets/plugins/fontawesome-free/css/all.min.css')  }}">
    <link rel="stylesheet" href="{{  asset('assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css')  }}">

    <link rel="stylesheet" href="{{  asset('assets/leaflet/leaflet.css')  }}">
</head>


<style>
.leaflet-control-attribution {
    font-size: 9px !important;
}

.leaflet-control-attribution a {
    font-size: 9px !important;
}

@keyframes heartbeat {
    0% {
        transform: scale(.80);
    }

    20% {
        transform: scale(1);
    }

    40% {
        transform: scale(.80);
    }

    60% {
        transform: scale(1);
    }

    80% {
        transform: scale(.80);
    }

    100% {
        transform: scale(.80);
    }
}

.preloader {
    height: 100vh;
    width: 100vw;
    background: #FFF;
    white-space: nowrap;
    text-align: center;
    position: fixed;
    z-index: 9999999;
}

.helper {
    display: inline-block;
    height: 100vh;
    vertical-align: middle;
}

.heartbeating {
    vertical-align: middle;
    max-height: 90px;
    max-width: 90px;
    animation: heartbeat 2s infinite;
}
</style>

<div class="preloader">
    <div class="loading">
        <span class="helper"></span><img class="heartbeating" src="{{  asset('assets/img/logo/bkkbn-small.png')  }}">
    </div>
</div>


@if (Session::get('alert'))
<div class="alert alert-{{ Session::get('color') }} m-1 alert-dismissible fade show"
    style="position:fixed;right:0;z-index:9999;" role="alert">
    <span class="alert-heading"><b>{{ Session::get('title') }}</b></span>

    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <p class="mb-0 text-white">{{ Session::get('message') }}</p>

</div>
@endif

<body>
    <div id="app" class="h-100">
        <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-default fixed-top">


            <button class="btn btn-sm btn-light d-inline-block d-md-none mobile-nav" type="button"
                data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars d-inline-block d-md-none mobile-nav mr-2"></i>Menu
            </button>

            <a class="navbar-brand" href="#"><img style="width:120px" src="{{  asset('assets/img/logo/logo-06.png')  }}"
                    alt="Monalisa"></a>

            <div class="collapse navbar-collapse mt-2 mb-5 my-lg-0 ml-2" id="navbarSupportedContent">
                <hr class="d-lg-none mt-0">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/') }}">Beranda<span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item {{ request()->is('news') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('news') }}">Berita<span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item {{ request()->is('profil-wilayah') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('training.profile') }}">Basis Wilayah</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('diklat/*') ? 'active' : '' }}" href="#"
                            id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            Data Mekop
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                            <style>
                            .dropright:hover .dropdown-menu {
                                display: block !important;
                            }
                            </style>


                            <a class="dropdown-item {{ request()->is('statistic/pkb') ? 'active' : '' }}"
                                href="{{ route('statistic.pkb') }}">Rasio PKB dan PLKB</a>

                            <a class="dropdown-item {{ request()->is('statistic/ppkbd') ? 'active' : '' }}"
                                href="{{ route('statistic.ppkbd') }}">Rasio PPKBD</a>

                            <a class="dropdown-item {{ request()->is('statistic/realize') ? 'active' : '' }}"
                                href="{{ route('statistic.realize') }}">Pelakasanaan Mekop</a>


                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item {{ request()->is('statistic/performed') ? 'active' : '' }}"
                                href="{{ route('statistic.performed') }}">PKB dan PLKB Terlaksana</a>

                            <a class="dropdown-item {{ request()->is('statistic/existance') ? 'active' : '' }}"
                                href="{{ route('statistic.existance') }}">Keberadaan Peserta KB</a>
                        </div>
                    </li>

                    <!-- Authentication Links -->
                    @if(!Auth::user())
                    <!--  <li class="nav-item">
                        <a class="nav-link {{ request()->is('register') ? 'active' : '' }}"
                            href="{{ route('registerform') }}">Pendaftaran</a>
                    </li> -->

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('login') ? 'active' : '' }}"
                            href="{{ route('loginform') }}">Masuk</a>
                    </li>

                    @else

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Hai, {{ Auth::user()->first_name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                            @if(Auth::user()->hasRole('widyaiswara'))
                            <a class="dropdown-item" href="{{ route('student.dashboard') }}">
                                Panel Widyaiswara
                            </a>
                            @endif

                            @if(Auth::user()->hasRole('admin'))
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                Panel Admin
                            </a>
                            @endif

                            @if(Auth::user()->hasRole('Adminprovinsi'))
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                Panel Admin
                            </a>
                            @endif
                            @if(Auth::user()->hasRole('Admindaerah'))
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                Panel Informasi Daerah
                            </a>
                            @endif

                            @if(Auth::user()->hasRole('superadmin'))
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                Panel Superadmin
                            </a>
                            @endif

                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Keluar
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                    @endif


                </ul>
            </div>
        </nav>

        <main role="main" class="pt-5 min-vh-100">
            @yield('content')
        </main>

        <nav class="navbar navbar-expand-lg navbar-light bg-light footer absolute-bottom">
            <p class="p_proper-screen_md m-1">Copyright &copy <script>
                document.write(new Date().getFullYear())
                </script> Lini Lapangan | Badan Kependudukan dan Keluarga Berencana Nasional</p>

            <div class="collapse navbar-collapse py-2 my-lg-0" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="#">Privacy Policy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            Term & Condition
                        </a>
                    </li>

                </ul>
            </div>
        </nav>

    </div>

</body>

<!-- jQuery -->
<script src="{{  asset('assets/plugins/jquery/jquery.min.js')  }}"></script>
<script src="{{  asset('assets/plugins/nicescroll-bootstrap/jquery-nicescroll.js')  }}"></script>
<!-- Bootstrap 4 -->
<script src="{{  asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')  }}"></script>
<!-- Chart -->
<script src="{{  asset('assets/plugins/chart.js/Chart.min.js')  }}"></script>
<!-- DataTables -->
<script src="{{  asset('assets/plugins/datatables/jquery.dataTables.js')  }}"></script>
<script src="{{  asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.js')  }}"></script>

<!-- Bootstrap4 Duallistbox -->
<script src="{{  asset('assets/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')  }}"></script>
<script src="{{  asset('assets/plugins/jquery-validation/jquery.validate.min.js')  }}"></script>
<script src="{{  asset('assets/plugins/jquery-validation/additional-methods.min.js')  }}"></script>
<!-- AdminLTE App -->
<script src="{{  asset('assets/dist/js/adminlte.min.js')  }}"></script>
<script src="{{  asset('assets/dist/js/jquery.mask.js')  }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{  asset('assets/dist/js/demo.js')  }}"></script>
<!-- Summernote -->
<script src="{{  asset('assets/plugins/summernote/summernote-bs4.min.js')  }}"></script>
<script src="{{  asset('assets/js/bootstrap-datepicker.js')  }}"></script>
<script src="{{  asset('assets/js/croppie.js')  }}"></script>
<script src="{{  asset('assets/js/orgchart.js')  }}"></script>
<script src="{{  asset('assets/js/main.js')  }}"></script>
<script src="{{  asset('assets/js/jquery-id_dateformatter.js')  }}"></script>

<script src="{{  asset('assets/dist/js/pages/extras-script.js')  }}"> </script>
<script src="{{  asset('assets/pdf/pdf.js')  }}"></script>
<script src="{{  asset('assets/pdf/pdf.worker.js')  }}"></script>
<script src="{{  asset('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')  }}"></script>

<script src="{{  asset('assets/leaflet/leaflet.js')  }}"></script>

@yield('javascript')


<script>
$(document).ready(function() {
    $(".preloader").fadeOut();
});
</script>

</html>