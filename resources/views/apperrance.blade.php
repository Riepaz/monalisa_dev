@extends('layouts.backend.backend-template')

@section('content')

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <div class="content-wrapper pt-5">
            <!-- Main content -->

            <section class="content">

                @if ($errors->any())
                <div class="card bg-danger show p-3" role="alert">
                    <h5><strong class="ml-4">Kesalahan Input!</strong></h5><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"><a class="nav-link active" href="#video"
                                                data-toggle="tab">Video</a></li>

                                        <!-- <li class="nav-item"><a class="nav-link" href="#account"
                                                data-toggle="tab">Pengaturan Akun</a></li> -->

                                    </ul>
                                </div><!-- /.card-header -->
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="video">
                                            <form class="form-horizontal row align-items-center justify-content-center"
                                                action="{{ route('panel.updateytvideo') }}" method="POST"
                                                enctype="multipart/form-data" id="account_settings">
                                                @csrf

                                                <div class="col-lg-6 col-12">
                                                    <div
                                                        class="col-12 bg-vid d-flex align-items-center justify-content-center">
                                                        <iframe width="450" height="250" class="card d-vid my-4"
                                                            src="{{ $yt_display->value }}?nocahce={{ date('YmdHis') }}"
                                                            frameborder="0"
                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                            allowfullscreen>
                                                        </iframe>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-12">
                                                    <input type="text" id="ytlinkvideo" name="ytlinkvideo"
                                                        value="{{ $yt_display->value }}"
                                                        class="form-control form-control-sm">
                                                    <div class="form-group row mt-2">
                                                        <div class="col-12">
                                                            <button type="submit" id="add_configuration"
                                                                class="btn btn-outline-info btn-block">Ubah</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                            </form>
                                        </div>

                                        <!-- <div class="tab-pane" id="account">
                                            <form class="form-horizontal" action="{{ route('panel.config.account') }}"
                                                id="account_settings">
                                                @csrf

                                                <div class="form-group row">
                                                    <label for="username"
                                                        class="col-sm-2 col-form-label text-sm">Username</label>
                                                    <div class="col-sm-10">
                                                        <input type="username" class="form-control form-control-sm"
                                                            id="username" name="username" placeholder="Username"
                                                            value="{{ \Auth::user()->username }}" readonly required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="password" class="col-sm-2 col-form-label">Kata
                                                        Sandi Baru</label>
                                                    <div class="col-sm-10">
                                                        <input type="password" class="form-control form-control-sm"
                                                            id="password" name="password" placeholder="Password"
                                                            value="">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="c_password" class="col-sm-2 col-form-label">Kata
                                                        Konfirmasi Sandi Baru</label>
                                                    <div class="col-sm-10">
                                                        <input type="password" class="form-control form-control-sm"
                                                            id="c_password" name="c_password"
                                                            placeholder="Konfirmasi Kata Sandi" value="">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <button type="submit" id="add_configuration"
                                                            class="btn btn-outline-info btn-block">Ubah</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div> -->

                                    </div>
                                    <!-- /.tab-content -->
                                </div><!-- /.card-body -->
                            </div>
                            <!-- /.nav-tabs-custom -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
</body>


<div id="uploadimageModal" class="modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="width:650px;">
            <div class="modal-header">
                <h5 class="modal-title">Sesuaikan Foto</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <div id="image_preview"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="batal_crop">Batal</button>
                <button class="btn btn-success crop_image">Crop</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

jQuery(document).ready(function() {

    jQuery('select[name="gender"]').val('{{ Auth::user()->gender }}');

    jQuery('select[name="province_id"]').on('change', function() {
        var provinceID = jQuery(this).val();
        if (provinceID) {
            jQuery.ajax({
                // url: 'province/getregency/' + provinceID,
                url: '{{url("province/getregency/")}}' + '/' + provinceID,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    jQuery('select[name="regency_id"]').empty();
                    jQuery.each(data, function(key, value) {
                        $('select[name="regency_id"]').append('<option value="' +
                            key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('select[name="regency_id"]').empty();
        }
    });


    jQuery('select[name="regency_id"]').on('change', function() {
        var regencyID = jQuery(this).val();
        if (regencyID) {
            jQuery.ajax({
                url: '{{url("regency/getdistrict/")}}' + '/' + regencyID,

                type: "GET",
                dataType: "json",
                success: function(data) {
                    jQuery('select[name="district_id"]').empty();
                    jQuery.each(data, function(key, value) {
                        $('select[name="district_id"]').append('<option value="' +
                            key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('select[name="district_id"]').empty();
        }
    });


    jQuery('select[name="district_id"]').on('change', function() {
        var villageID = jQuery(this).val();
        if (villageID) {
            jQuery.ajax({
                url: '{{url("district/getvillage/")}}' + '/' + villageID,
                // url: 'district/getvillage/' + villageID,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    jQuery('select[name="village_id"]').empty();
                    jQuery.each(data, function(key, value) {
                        $('select[name="village_id"]').append('<option value="' +
                            key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('select[name="village_id"]').empty();
        }
    });





});
</script>

<script>
function isProvince(that) {
    if (that.value != "") {
        document.getElementById("ifProvince").style.display = "block";
    } else {
        document.getElementById("ifProvince").style.display = "none";
    }
}

function isPKB(that) {
    if (that.value == "2") {
        document.getElementById("ifPKB").style.display = "block";
    } else {
        document.getElementById("ifPKB").style.display = "none";
    }
}
</script>


@endsection