<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Monalisa | PANEL</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{  asset('assets/img/logo/bkkbn-big.png')  }}" type="image/png">
    <!-- Font Awesome -->

    <!-- Ionicons -->
    <!-- DataTables -->
    <link rel="stylesheet" href="{{  asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.css')  }}">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="{{  asset('assets/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')  }}">
    <link rel="stylesheet" href="{{  asset('assets/plugins/daterangepicker/daterangepicker.css')  }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{  asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')  }}">
    <link rel="stylesheet" href="{{  asset('assets/dist/css/adminlte.min.css')  }}">

    <link rel="stylesheet" href="{{  asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')  }}">
    <link rel="stylesheet" href="{{  asset('assets/css/bootstrap-datepicker3.css')  }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{  asset('assets/plugins/summernote/summernote-bs4.css')  }}">
    <!-- Google Font: Source Sans Pro -->

    <link rel="stylesheet" href="{{  asset('assets/css/style.css')  }}">
    <link rel="stylesheet" href="{{  asset('assets/plugins/fontawesome-free/css/all.min.css')  }}">
    <link rel="stylesheet" href="{{  asset('assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css')  }}">

    <link rel="stylesheet" href="{{  asset('assets/leaflet/leaflet.css')  }}">

    <style>
    .leaflet-control-attribution {
        font-size: 9px !important;
    }

    .leaflet-control-attribution a {
        font-size: 9px !important;
    }
    </style>

</head>

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
    <div id="app">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>

                <!-- Notifications Dropdown Menu -->
                <!-- <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell" style="font-size:25px;"></i>
                        <span class="badge badge-warning navbar-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-left">
                        <span class="dropdown-item dropdown-header">3 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> 3 new reports
                            <span class="float-right text-muted text-sm">2 days</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                    </div>
                </li> -->

                <!-- Notifications Dropdown Menu -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">
                        <i class="fa fa-home" style="font-size:25px;"></i>
                    </a>
                </li>

            </ul>

            <!-- SEARCH FORM -->
            <form class="form-inline ml-3">
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-navbar" type="search" placeholder="Cari..."
                        aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>

        </nav>
        <!-- /.navbar -->


        <!-- Main Sidebar Container -->
        <aside class="main-sidebar navbar-light bg-light navbar-default elevation-4">
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel d-flex bg-gradient-info py-4 rounded-bottom">
                    <div class="image align-self-center mr-1">
                        <h2><i class="fas fa-user-circle"></i></h2>
                    </div>
                    <div class="info align-self-center ">
                        <a href="{{ route('panel.config') }}"
                            class="d-block text-white"><b>{{ Auth::user()->first_name }}</b></a>
                        <span class="">{{ Auth::user()->getRole() }}</span><br>
                        <small class="">{{ Auth::user()->getProvince() }}</small>
                    </div>

                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        <!-- SUPERADMIN MENU -->
                        @if(Auth::user()->hasRole('superadmin') or Auth::user()->hasRole('admin') or
                        Auth::user()->hasRole('adminprovinsi') or Auth::user()->hasRole('admindaerah'))
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="nav-link  {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-th"></i>
                                <p>
                                    Dashboard  
                                </p>
                            </a>
                        </li>

                        @if(Auth::user()->hasRole('superadmin') or Auth::user()->hasRole('admin'))
                        <li class="nav-item">
                            <a href="{{ route('admin.news') }}"
                                class="nav-link  {{ request()->is('admin/news') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-blog"></i>
                                <p>
                                    Berita
                                </p>
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->hasRole('superadmin') or Auth::user()->hasRole('admin') or
                        Auth::user()->hasRole('adminprovinsi'))
                        <li class="nav-item">
                            <a href="{{ route('admin.info') }}"
                                class="nav-link  {{ request()->is('admin/info') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-info"></i>
                                <p>
                                    Informasi Utama
                                </p>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->hasRole('superadmin') or Auth::user()->hasRole('admin'))
                        <li
                            class="nav-item has-treeview {{ request()->is('admin/administrative/*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="fas fa-sliders-h nav-icon"></i>
                                <p>
                                    Administrasi
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                @if(Auth::user()->hasRole('superadmin') or Auth::user()->hasRole('admin'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.fiscal') }}"
                                        class="nav-link {{ request()->is('admin/administrative/fiscal') ? 'active' : '' }}">
                                        <i class="fas fa-calendar-alt nav-icon"></i>
                                        <p>Tahun Anggaran</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.categories') }}"
                                        class="nav-link {{ request()->is('admin/administrative/categories') ? 'active' : '' }}">
                                        <i class="fas fa-list-alt nav-icon"></i>
                                        <p>Indikator Mekop</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.event') }}"
                                        class="nav-link {{ request()->is('admin/administrative/event') ? 'active' : '' }}">
                                        <i class="fas fa-list-alt nav-icon"></i>
                                        <p>Butir Kegiatan Mekop</p>
                                    </a>
                                </li>
                                @endif

                            </ul>
                        </li>
                        @endif

                        @if(Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('adminprovinsi'))
                        <li class=" nav-item">
                            <a href="{{ route('admin.users') }}"
                                class="nav-link {{ request()->is('admin/users/users') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users-cog"></i>
                                <p>
                                    Pengguna
                                </p>
                            </a>
                        </li>
                        @endif
                        @endif
                        <!-- SUPERADMIN MENU -->


                        <li class="nav-item has-treeview {{ request()->is('config/*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="fas fa-cog nav-icon"></i>
                                <p>
                                    Pengaturan
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="{{ route('panel.config') }}"
                                        class="nav-link  {{ request()->is('config/account') ? 'active' : '' }}">
                                        <i class="fas fa-user-cog nav-icon"></i>
                                        <p>Pengaturan Akun</p>
                                    </a>
                                </li>
                                @if(Auth::user()->hasRole('superadmin') or Auth::user()->hasRole('admin'))
                                <li class="nav-item">
                                    <a href="{{ route('panel.apperrances') }}"
                                        class="nav-link {{ request()->is('config/apperrances') ? 'active' : '' }}">
                                        <i class="fas fa-video nav-icon"></i>
                                        <p>Tampilan</p>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>


                        <li class="nav-item">

                            <a class="nav-link" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="nav-icon fas fa-power-off"></i>
                                <p>
                                    Keluar
                                </p>
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>



        <body>
            <main role="main">
                @yield('content')
            </main>
        </body>

    </div>
</body>
<script src="{{  asset('assets/plugins/jquery/jquery.min.js')  }}"></script>
<script src="{{  asset('assets/plugins/nicescroll-bootstrap/jquery-nicescroll.js')  }}"></script>
<script src="{{  asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')  }}"></script>
<script src="{{  asset('assets/plugins/chart.js/Chart.min.js')  }}"></script>
<script src="{{  asset('assets/plugins/datatables/jquery.dataTables.js')  }}"></script>
<script src="{{  asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.js')  }}"></script>
<script src="{{  asset('assets/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')  }}"></script>
<script src="{{  asset('assets/plugins/jquery-validation/jquery.validate.min.js')  }}"></script>
<script src="{{  asset('assets/plugins/jquery-validation/additional-methods.min.js')  }}"></script>
<script src="{{  asset('assets/dist/js/adminlte.min.js')  }}"></script>
<script src="{{  asset('assets/dist/js/jquery.mask.js')  }}"></script>
<script src="{{  asset('assets/dist/js/demo.js')  }}"></script>
<script src="{{  asset('assets/plugins/summernote/summernote-bs4.min.js')  }}"></script>
<script src="{{  asset('assets/plugins/moment/moment.min.js')  }}"></script>
<script src="{{  asset('assets/plugins/daterangepicker/daterangepicker.js')  }}"></script>

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
$(".sidebar").overlayScrollbars({});
$(".XWrapper").overlayScrollbars({});
$(".YWrapper").overlayScrollbars({});
</script>

<script>
$(document).ready(function() {
    $('#preloader').html('')
    $('#container').attr('hidden', false);
});
</script>

</html>