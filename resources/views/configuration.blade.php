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

                        <!-- /.col -->
                        <div class="col-md-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"><a class="nav-link active" href="#privacy"
                                                data-toggle="tab">Data Pribadi</a></li>

                                        <li class="nav-item"><a class="nav-link" href="#account"
                                                data-toggle="tab">Pengaturan Akun</a></li>

                                    </ul>
                                </div><!-- /.card-header -->
                                <div class="card-body">
                                    <div class="tab-content">

                                        <div class="active tab-pane" id="privacy">
                                            <form class="form-horizontal" action="{{ route('panel.config.user') }}"
                                                id="account_settings">
                                                @csrf

                                                <input id="username" name="username" type="username"
                                                    value="{{ Auth::user()->username }}" hidden readonly>
                                                <input id="email" name="email" type="email"
                                                    value="{{ Auth::user()->email }}" hidden readonly>
                                                <input id="reg_number" name="reg_number" type="reg_number"
                                                    value="{{ Auth::user()->reg_number }}" hidden readonly>

                                                @if(Auth::user()->reg_number != "")
                                                <div class="form-group row">
                                                    <label for="reg_number"
                                                        class="col-sm-2 col-form-label text-sm">NIP</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="reg_number" name="reg_number" placeholder="NIP"
                                                            value="{{ \Auth::user()->reg_number }}" readonly required>
                                                    </div>
                                                </div>
                                                @endif

                                                @if(Auth::user()->type == 2)
                                                <div class="form-group row">
                                                    <label for="job_free"
                                                        class="col-sm-2 col-form-label text-sm">Jabatan Atau
                                                        Bagian Pekerjaan</label>

                                                    <div class="col-sm-10">
                                                        <input id="job_free" name="job_free" type="text"
                                                            class="form-control form-control-sm"
                                                            value="{{ Auth::user()->job }}"
                                                            placeholder="Jabatan atau Bagian" name="job_free">
                                                    </div>
                                                </div>

                                                <hr>
                                                @endif


                                                <div class="form-group row">
                                                    <label for="first_name" class="col-sm-2 col-form-label text-sm">Nama
                                                        Awal</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="first_name" name="first_name" placeholder="Nama awal"
                                                            value="{{ \Auth::user()->first_name }}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="last_name" class="col-sm-2 col-form-label text-sm">Nama
                                                        Akhir</label>
                                                    <div class="col-sm-10">
                                                        <input type="last_name" class="form-control form-control-sm"
                                                            id="last_name" name="last_name" placeholder="Nama akhir"
                                                            value="{{ \Auth::user()->last_name }}">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="age"
                                                        class="col-sm-2 col-form-label text-sm">Usia</label>
                                                    <div class="col-sm-10">
                                                        <input type="number" class="form-control form-control-sm"
                                                            id="age" name="age" placeholder="Nama akhir"
                                                            value="{{ \Auth::user()->age }}">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="gender" class="col-sm-2 col-form-label text-sm">Jenis
                                                        Kelamin</label>
                                                    <div class="col-sm-10">
                                                        <select name="gender" id="gender"
                                                            class="form-control form-control-sm form-control form-control-sm-sm"
                                                            style="width:200px; width:100%;">
                                                            <option value="male">Laki - Laki</option>
                                                            <option value="female">Perempuan</option>

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="email"
                                                        class="col-sm-2 col-form-label text-sm">Email</label>
                                                    <div class="col-sm-10">
                                                        <input type="email" class="form-control form-control-sm"
                                                            id="email" name="email" placeholder="Email"
                                                            value="{{ \Auth::user()->email }}"
                                                            readonly="@if(Auth::user()->email)? true : false @endif">
                                                    </div>
                                                </div>


                                                <div class="form-group row">
                                                    <label for="phone" class="col-sm-2 col-form-label text-sm">No
                                                        WhatsApp</label>
                                                    <div class="col-sm-10">
                                                        <input type="tel" class="form-control form-control-sm"
                                                            id="phone" name="phone" placeholder="No WhatsApp"
                                                            value="{{ \Auth::user()->phone }}" maxlength="15">
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="form-group">
                                                    <label class="text-sm">Alamat <span
                                                            class="required-ast"></span></label>
                                                    <textarea type="text" class="form-control form-control-sm"
                                                        placeholder="Alamat E.g Jalan | Komplek | RT/RW"
                                                        id="address_street" name="address_street"></textarea>
                                                    @if ($errors->has('address'))
                                                    <p class="text-sm text-red" class="error" for="first_name">
                                                        {{ $errors->first('address') }}</p>
                                                    @endif
                                                </div>
                                                <div class="form-group">
                                                    <label class="text-sm" for="province">Pilih Provinsi : <span
                                                            class="required-ast">
                                                        </span></label>
                                                    <select name="province_id" id="province_id"
                                                        class="form-control form-control-sm form-control form-control-sm-sm"
                                                        style="width:200px; width:100%;">
                                                        <option value="">--- Pilih Provinsi ---</option>
                                                        @foreach ($provinces as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">

                                                    <label class="text-sm" for="regency">Pilih Kabupaten : <span
                                                            class="required-ast"></span></label>
                                                    <select name="regency_id" id="regency_id"
                                                        class=" form-control form-control-sm form-control form-control-sm-sm"
                                                        style="width:200px; width:100%;">
                                                        <option>--Kabupaten--</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="text-sm" for="district">Pilih Kecamatan : </label>
                                                    <select name="district_id" id="district"
                                                        class="form-control form-control-sm form-control form-control-sm-sm"
                                                        style="width:200px; width:100%;">
                                                        <option>--Kecamatan--</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="text-sm" for="village">Pilih Desa:</label>
                                                    <select name="village_id" id="village_id"
                                                        class="form-control form-control-sm form-control form-control-sm-sm"
                                                        style="width:200px; width:100%;">
                                                        <option>--Desa--</option>

                                                    </select>
                                                </div>

                                                <hr>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <button type="submit" id="add_configuration"
                                                            class="btn btn-outline-info btn-block">Ubah</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane" id="account">
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
                                        </div>

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
                async: false,
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
                async: false,
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
                async: false,
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


    $('#address_street').val('{{ Auth::user()->address_street }}');

    if ("{{ Auth::user()->hasRole('adminprovinsi') }}" || "{{ Auth::user()->hasRole('admindaerah') }}") {
        $('select[name="province_id"]').attr('disabled', true);
        $('select[name="regency_id"]').attr('disabled', true);
        $('select[name="district_id"]').attr('disabled', true);
        $('select[name="village_id"]').attr('disabled', true);

        $('#alert_status').html(
            '<div class="alert-info p-3"><p>Karena Anda sebagai <b>Admin Provinsi</b> Anda tidak dapat mengubah seluruh alamat.</p></div>'
        );

    }

    $('select[name="province_id"]').val('{{Auth::user()->province_id}}').change();
    $('select[name="regency_id"]').val('{{Auth::user()->regency_id}}').change();
    $('select[name="district_id"]').val('{{Auth::user()->district_id}}').change();
    $('select[name="village_id"]').val('{{Auth::user()->village_id}}').change();


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