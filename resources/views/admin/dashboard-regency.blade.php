@extends('layouts.backend.backend-template')

@section('content')

<style>
.chartWrapper {
    position: relative;
}

.chartWrapper>canvas {
    position: absolute;
    left: 0;
    top: 0;
    pointer-events: none;
}

.XWrapper {
    width: 100%;
    overflow-x: scroll;
}

.YWrapper {
    padding-right: 20px;
    height: 350px;
    overflow-y: scroll;
}
</style>

<body class="hold-transition sidebar-mini layout-fixed">

    <div class="wrapper">

        <div class="content-wrapper pt-3">
        <section class="content py-1 px-2">
            <h6 id="preloader" class=" text-center">
                <i class="fas fa-spinner fa-spin"></i>
            </h6>
            <div id="container" class="row p-4" hidden>

                <div class="col-lg-12 col-12 mt-1">

                    <div class="card card-outline card-info mt-0 h-100 py-4 px-2 text-sm">

                        <h5 id="table_title">Semua Data</h5>
                        <small id="table_subtitle">Menampilkan semua data berdasarkan filter</small>
                        <hr>

                        <form id="form_statistictarget" method="POST" action="{{ route('exportrealize.realize') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <select name="province_id" id="province_id" class="form-control form-control-sm my-1" hidden>
                                <option value="null" selected>--Semua Provinsi--</option>
                            </select>
                            <select name="regency_id" id="regency_id" class="form-control form-control-sm my-1" hidden>
                                <option value="null" selected>--Semua Kota Kabupaten--</option>
                            </select>
                            <select name="district_id" id="district_id" class="form-control form-control-sm my-1" disabled>
                                <option value="null" selected>--Semua Kecamatan--</option>
                            </select>
                            <button type="submit" id="btn_submit" class="btn btn-warning btn-sm my-3 w-100">Eksport</button>

                        </form>

                        <hr>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active"><a href="#">Daerah</a></li>
                            </ol>
                        </nav>

                        <div id="tbl_district_container" class="d-none">
                            <table
                                class="border-0 shadow-none table table-striped table-bordered table-responsive table-hover text-sm w-100 mekop_district_table"
                                width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-light">
                                        <th rowspan="2" class="text-center">No</th>
                                        <th rowspan="2" class="text-center">Kode</th>
                                        <th rowspan="2" class="text-center">Kecamatan</th>
                                        <th colspan="3" class="text-center">Mekanisme Operasional</th>
                                    </tr>
                                    <tr class="table-light">
                                        <th class="text-center">Staff Meeting</th>
                                        <th class="text-center">Rakor Kecamatan</th>
                                        <th class="text-center">Minilok</th>
                                    </tr>
                                    <tr class="table-light">
                                        <td class="text-center">(1)</td>
                                        <td class="text-center">(2)</td>
                                        <td class="text-center">(3)</td>
                                        <td class="text-center">(4)</td>
                                        <td class="text-center">(5)</td>
                                        <td class="text-center">(6)</td>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div id="tbl_village_container" class="d-none">
                            <table
                                class="border-0 shadow-none table table-striped table-bordered table-responsive table-hover text-sm w-100 mekop_village_table"
                                width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-light">
                                        <th rowspan="2" class="text-center">No</th>
                                        <th rowspan="2" class="text-center">Kode</th>
                                        <th rowspan="2" class="text-center">Desa</th>
                                        <th colspan="6" class="text-center">Mekanisme Operasional</th>
                                    </tr>
                                    <tr class="table-light">
                                        <th class="text-center">Rakordes</th>
                                        <th class="text-center">Pertemuan Pokja Kamp KB</th>
                                        <th class="text-center">Pembinaan IMP</th>
                                        <th class="text-center">Pencatatan dan Pelaporan</th>
                                        <th class="text-center">KIE</th>
                                        <th class="text-center">Pelayanan</th>
                                    </tr>
                                    <tr class="table-light">
                                        <td class="text-center">(1)</td>
                                        <td class="text-center">(2)</td>
                                        <td class="text-center">(3)</td>
                                        <td class="text-center">(4)</td>
                                        <td class="text-center">(5)</td>
                                        <td class="text-center">(6)</td>
                                        <td class="text-center">(7)</td>
                                        <td class="text-center">(8)</td>
                                        <td class="text-center">(9)</td>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

        </section>
        </div>
</body>
@endsection

@section('javascript')

<script type="text/javascript">
var mekop_regency_table;
var mekop_district_table;
var mekop_village_table;

$(document).ready(function() {
    $(document).ready(function() {
        $.ajax({
            url: "{{ route('provinces.get') }}",
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('select[name="province_id"]').empty();
                $('select[name="province_id"]').append(
                    '<option value="null" selected>--Semua Provinsi--</option>');
                $.each(data, function(key, value) {
                    $('select[name="province_id"]').append('<option value="' + key +
                        '">' +
                        value + '</option>');
                });

                $('select[name="province_id"]').val('{{ Auth::user()->province_id }}');
                $('select[name="province_id"]').change();
            }
        });
    });

    $('select[name="province_id"]').on('change', function() {
        var provinceID = jQuery(this).val();
        if (provinceID) {
            jQuery.ajax({
                url: "{{ url('/province/getregency/') }}" + "/" + provinceID,
                type: "GET",
                dataType: "json",
                async: false,
                success: function(data) {
                    jQuery('select[name="regency_id"]').empty();
                    $('select[name="regency_id"]').append(
                        '<option value="null" selected>--Semua Kota Kabupaten--</option>'
                    );
                    jQuery.each(data, function(key, value) {
                        $('select[name="regency_id"]').append('<option value="' +
                            key + '">' + value + '</option>');
                    });

                    $('select[name="regency_id"]').val('{{ Auth::user()->regency_id }}');
                    $('select[name="regency_id"]').change();
                }
            });
        } else {
            $('select[name="regency_id"]').empty();
        }

    });

    $('select[name="regency_id"]').on('change', function() {
        var regencyID = jQuery(this).val();
        if (regencyID) {
            jQuery.ajax({
                url: "{{ url('/regency/getdistrict/') }}" + "/" + regencyID,
                type: "GET",
                dataType: "json",
                async: false,
                success: function(data) {
                    jQuery('select[name="district_id"]').empty();
                    $('select[name="district_id"]').append(
                        '<option value="null" selected>--Semua Kecamatan--</option>');
                    jQuery.each(data, function(key, value) {
                        $('select[name="district_id"]').append('<option value="' +
                            key + '">' + value + '</option>');
                    });

                    changeResetHandler();
                }
            });
        } else {
            $('select[name="district_id"]').empty();
        }
    });

    $('select[name="district_id"]').on('change', function() {
        changeResetHandler();
    });

    function changeResetHandler() {
        if ($('select[name="province_id"]').val() != 'null' && $('select[name="regency_id"]').val() != 'null') {
            $('select[name="district_id"]').attr('disabled', false);
            openTblDistrict(null, $('select[name="regency_id"]').find(':selected').text());
        }

        if ($('select[name="province_id"]').val() != 'null' &&
            $('select[name="regency_id"]').val() != 'null' &&
            $('select[name="district_id"]').val() != 'null'
        ) {
            openTblVillage(null, $('select[name="district_id"]').find(':selected').text());
        }
    }
});


function openTblDistrict(id, name) {

    if ($('.breadcrumb').find('.breadcrumb-item').hasClass('bcvillage')) {
        $('.breadcrumb').find('.breadcrumb-item').remove('.bcvillage');
    }

    if (!$('.breadcrumb').find('.breadcrumb-item').hasClass('bcdistrict')) {
        $('.breadcrumb').find('.breadcrumb-item').removeClass('active');
        $('.breadcrumb').append(
            '<li class="bcdistrict breadcrumb-item active"  onclick="openTblDistrict(' + id + ' , ' + "'" + name +
            "'" +
            ')" aria-current="page"><a href="#">Kecamatan</a></li>'
        );
    }

    $('#table_title').html(name);
    $('#table_subtitle').html('Menampilkan data - data ' + name);

    $('#tbl_regency_container').addClass('d-none');
    $('#tbl_district_container').removeClass('d-none');
    $('#tbl_village_container').addClass('d-none');

    if (mekop_district_table != null) {
        mekop_district_table.fnDestroy();
    }

    mekop_district_table = $('.mekop_district_table').dataTable({
        initComplete: function() {
            var input = $('#' + $(".mekop_district_table").attr('id') + '_filter input').unbind(),
                self = this.api(),
                searchButton = $('<button>')
                .html('<i class="fas fa-search"></i>')
                .addClass('btn btn-outline-info btn-sm mx-1')
                .click(function() {
                    self.search(input.val()).draw();
                });

            $('#' + $(".mekop_district_table").attr('id') + '_filter').append(searchButton);
        },
        "processing": true,
        "responsive": true,
        "serverSide": true,
        "pageLength": 20,

        "ajax": {
            "url": "{{ route('getallstatrealizedistrict.realize') }}",
            "type": "POST",
            "headers": {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            "data": function(data) {
                data.regency_id = id != null ? id : $('select[name="regency_id"]').val();
            },
        },
        "order": [
            [0, "asc"]
        ],
        'columnDefs': [{
                "targets": 0,
                "width": "5%",
                "className": "text-center",
            },
            {
                "targets": 1,
                "width": "10%",
                "className": "text-center",
            },
            {
                "targets": 2,
                "width": "25%",
                "className": "text-left",
            },
            {
                "targets": 3,
                "width": "5%",
                "className": "text-center",
            },
            {
                "targets": 4,
                "width": "5%",
                "className": "text-center",
            },
            {
                "targets": 5,
                "width": "5%",
                "className": "text-center",
            },
        ],
        "language": {
            "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
            "sProcessing": '<i style="color:#2078bc;font-size:20px;" class="fas fa-spinner fa-spin mr-2"></i> Sedang memproses...',
            "sLengthMenu": "Tampilkan _MENU_",
            "sZeroRecords": "Tidak ditemukan data yang sesuai",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix": "",
            "sSearch": "Cari : ",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext": "Selanjutnya",
                "sLast": "Terakhir"
            }
        },

    });

}

function openTblVillage(id, name) {

    if (!$('.breadcrumb').find('.breadcrumb-item').hasClass('bcvillage')) {
        $('.breadcrumb').find('.breadcrumb-item').removeClass('active');
        $('.breadcrumb').append(
            '<li class="bcvillage breadcrumb-item active"  onclick="openTblVillage(null , ' + "'" + name + "'" +
            ')" aria-current="page"><a href="#">Desa Kelurahan</a></li>'
        );
    }

    $('#table_title').html('Kecamatan ' + name);
    $('#table_subtitle').html('Menampilkan data - data ' + name);

    $('#tbl_regency_container').addClass('d-none');
    $('#tbl_district_container').addClass('d-none');
    $('#tbl_village_container').removeClass('d-none');

    if (mekop_village_table != null) {
        mekop_village_table.fnDestroy();
    }

    mekop_village_table = $('.mekop_village_table').dataTable({
        initComplete: function() {
            var input = $('#' + $(".mekop_village_table").attr('id') + '_filter input').unbind(),
                self = this.api(),
                searchButton = $('<button>')
                .html('<i class="fas fa-search"></i>')
                .addClass('btn btn-outline-info btn-sm mx-1')
                .click(function() {
                    self.search(input.val()).draw();
                });

            $('#' + $(".mekop_village_table").attr('id') + '_filter').append(searchButton);
        },
        "processing": true,
        "responsive": true,
        "serverSide": true,
        "pageLength": 20,

        "ajax": {
            "url": "{{ route('getallstatrealizevillage.realize') }}",
            "type": "POST",
            "headers": {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            "data": function(data) {
                data.district_id = id != null ? id : $('select[name="district_id"]').val();
            },
        },
        "order": [
            [0, "asc"]
        ],
        'columnDefs': [{
                "targets": 0,
                "width": "5%",
                "className": "text-center",
            },
            {
                "targets": 1,
                "width": "10%",
                "className": "text-center",
            },
            {
                "targets": 2,
                "width": "25%",
                "className": "text-left",
            },
            {
                "targets": 3,
                "width": "5%",
                "className": "text-center",
            },
            {
                "targets": 4,
                "width": "5%",
                "className": "text-center",
            },
            {
                "targets": 5,
                "width": "5%",
                "className": "text-center",
            },
            {
                "targets": 6,
                "width": "5%",
                "className": "text-center",
            },
            {
                "targets": 7,
                "width": "5%",
                "className": "text-center",
            },
            {
                "targets": 8,
                "width": "5%",
                "className": "text-center",
            },
        ],
        "language": {
            "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
            "sProcessing": '<i style="color:#2078bc;font-size:20px;" class="fas fa-spinner fa-spin mr-2"></i> Sedang memproses...',
            "sLengthMenu": "Tampilkan _MENU_",
            "sZeroRecords": "Tidak ditemukan data yang sesuai",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix": "",
            "sSearch": "Cari : ",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext": "Selanjutnya",
                "sLast": "Terakhir"
            }
        },

    });
}
</script>
@endsection