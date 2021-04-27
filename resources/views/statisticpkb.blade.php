@extends('layouts.frontend.frontend-template')

@section('content')

<body class="h-100">
    <section class="content py-4 mt-4 px-4">


        <div class="row p-4">

            <div class="col-lg-3 col-12 mt-1 ">
                <div class="card bg-primary-01 px-3 py-4 border-0 shadow-none">

                    <div class="ml-2 my-3 offset-2 text-center">
                        <div class="my-2">
                            <h1 class="fas fas fa-user-shield text-info"></h1>
                        </div>
                        <p class="">Statistik</p>
                        <h5><b>{{ $title }}</b></h5>
                    </div>

                    <form id="form_statistictarget" method="POST" action="{{ route('exportpkb.pkb') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <select name="province_id" id="province_id" class="form-control form-control-sm my-1">
                            <option value="null" selected>--Semua Provinsi--</option>
                        </select>
                        <select name="regency_id" id="regency_id" class="form-control form-control-sm my-1" disabled>
                            <option value="null" selected>--Semua Kota Kabupaten--</option>
                        </select>
                        <button type="submit" id="btn_submit" class="btn btn-warning btn-sm my-3 w-100">Eksport</button>

                    </form>

                </div>
            </div>

            <div class="col-lg-9 col-12 mt-1">

                <div class="card card-outline card-info mt-0 h-100 py-4 px-2 text-sm">
                    <h5 id="table_title">Semua Provisi</h5>
                    <small id="table_subtitle">Menampilkan semua data berdasarkan provinsi</small>
                    <hr>

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Provinsi</a></li>
                        </ol>
                    </nav>

                    <div id="tbl_prov_container">
                        <table
                            class="border-0 shadow-none table table-striped table-bordered table-responsive table-hover text-sm w-100 mekop_province_table"
                            width="100%" cellspacing="0">
                            <thead class="border-light">
                                <tr class="bg-light">
                                    <th rowspan="2">No</th>
                                    <th rowspan="2" class="text-center">Kode</th>
                                    <th rowspan="2">Provinsi</th>
                                    <th rowspan="2" class="text-center">Jumlah Desa</th>
                                    <th colspan="4" class="text-center">Penyuluh KB / PLKB</th>
                                    <th rowspan="2" class="text-center">Rasio Terhadap Desa</th>
                                </tr>
                                <tr class="bg-light">
                                    <th class="text-center">Jumlah PKB</th>
                                    <th class="text-center">Jumlah PLKB</th>
                                    <th class="text-center">Jumlah PKB NON PNS</th>
                                    <th class="text-center">Total</th>
                                </tr>

                                <tr class="bg-light">
                                    <td class="text-center">(1)</td>
                                    <td class="text-center">(2)</td>
                                    <td class="text-center">(3)</td>
                                    <td class="text-center">(4)</td>
                                    <td class="text-center">(5)</td>
                                    <td class="text-center">(6)</td>
                                    <td class="text-center">(7)</td>
                                    <td class="text-center">(8)</td>
                                    <td class="text-center">(9 = 4 : 8)</td>
                                </tr>

                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>

                    <div id="tbl_regency_container" class="d-none">
                        <table
                            class="border-0 shadow-none table table-striped table-bordered table-responsive table-hover text-sm w-100 mekop_regency_table"
                            width="100%" cellspacing="0">
                            <thead class="border-light">
                                <tr class="bg-light">
                                    <th rowspan="2">No</th>
                                    <th rowspan="2" class="text-center">Kode</th>
                                    <th rowspan="2">Kota / Kabupaten</th>
                                    <th rowspan="2" class="text-center">Jumlah Desa</th>
                                    <th colspan="4" class="text-center">Penyuluh KB / PLKB</th>
                                    <th rowspan="2" class="text-center">Rasio Terhadap Desa</th>
                                </tr>
                                <tr class="bg-light">
                                    <th class="text-center">Jumlah PKB</th>
                                    <th class="text-center">Jumlah PLKB</th>
                                    <th class="text-center">Jumlah PKB NON PNS</th>
                                    <th class="text-center">Total</th>
                                </tr>

                                <tr class="bg-light">
                                    <td class="text-center">(1)</td>
                                    <td class="text-center">(2)</td>
                                    <td class="text-center">(3)</td>
                                    <td class="text-center">(4)</td>
                                    <td class="text-center">(5)</td>
                                    <td class="text-center">(6)</td>
                                    <td class="text-center">(7)</td>
                                    <td class="text-center">(8)</td>
                                    <td class="text-center">(9 = 4 : 8)</td>
                                </tr>

                            </thead>

                            <tbody>

                            </tbody>
                        </table>
                    </div>

                    <div id="tbl_district_container" class="d-none">
                        <table
                            class="border-0 shadow-none table table-striped table-bordered table-responsive table-hover text-sm w-100 mekop_district_table"
                            width="100%" cellspacing="0">
                            <thead>
                                <tr class="bg-light">
                                    <th rowspan="2">No</th>
                                    <th rowspan="2" class="text-center">Kode</th>
                                    <th rowspan="2">Kecamatan</th>
                                    <th colspan="3" class="text-center">Penyuluh KB / PLKB</th>
                                    <th rowspan="2" class="text-center">Total</th>

                                </tr>
                                <tr class="bg-light">
                                    <th class="text-center">Jumlah PKB</th>
                                    <th class="text-center">Jumlah PLKB</th>
                                    <th class="text-center">Jumlah PKB NON PNS</th>

                                </tr>

                                <tr class="bg-light">
                                    <td class="text-center">(1)</td>
                                    <td class="text-center">(2)</td>
                                    <td class="text-center">(3)</td>
                                    <td class="text-center">(4)</td>
                                    <td class="text-center">(5)</td>
                                    <td class="text-center">(6)</td>
                                    <td class="text-center">(7)</td>
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
</body>

@endsection


@section('javascript')

<script type="text/javascript">
var mekop_province_table;
var mekop_regency_table;
var mekop_district_table;

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

                changeResetHandler();
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

                    changeResetHandler();
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

    function changeResetHandler() {
        if ($('select[name="province_id"]').val() != 'null') {
            $('select[name="regency_id"]').attr('disabled', false);
            openTblRegency(null, $('select[name="province_id"]').find(':selected').text());
        } else {
            $('select[name="regency_id"]').attr('disabled', true);
            openTblProvince();
        }

        if ($('select[name="province_id"]').val() != 'null' && $('select[name="regency_id"]').val() != 'null') {
            openTblDistrict(null, $('select[name="regency_id"]').find(':selected').text());
        }
    }
});

function openTblProvince() {
    $('#table_title').html('Semua Provinsi');
    $('#table_subtitle').html('Menampilkan semua data berdasarkan provinsi');

    $('.breadcrumb').empty();
    $('.breadcrumb').append(
        '<li class="breadcrumb-item active" onclick="openTblProvince()" ><a href="#">Provinsi</a></li>');

    $('#tbl_prov_container').removeClass('d-none');
    $('#tbl_regency_container').addClass('d-none');
    $('#tbl_district_container').addClass('d-none');

    if (mekop_province_table != null) {
        mekop_province_table.fnDestroy();
    }

    mekop_province_table = $('.mekop_province_table').dataTable({
        initComplete: function() {
            var input = $('#' + $(".mekop_province_table").attr('id') + '_filter input').unbind(),
                self = this.api(),
                searchButton = $('<button>')
                .html('<i class="fas fa-search"></i>')
                .addClass('btn btn-outline-info btn-sm mx-1')
                .click(function() {
                    self.search(input.val()).draw();
                });

            $('#' + $(".mekop_province_table").attr('id') + '_filter').append(searchButton);
        },
        "processing": true,
        "responsive": true,
        "serverSide": true,
        "pageLength": 20,

        "ajax": {
            "url": "{{ route('getallstatpkbprov.pkb') }}",
            "type": "POST",
            "headers": {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            "data": function(data) {
                data.province_id = $('select[name="province_id"]').val();
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
                "width": "15%",
                "className": "text-center",
            },
            {
                "targets": 4,
                "width": "8%",
                "className": "text-center",
            },
            {
                "targets": 5,
                "width": "8%",
                "className": "text-center",
            },
            {
                "targets": 6,
                "width": "8%",
                "className": "text-center",
            },
            {
                "targets": 7,
                "width": "10%",
                "className": "text-center",
            },
            {
                "targets": 8,
                "width": "10%",
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

function openTblRegency(id, name) {

    if ($('.breadcrumb').find('.breadcrumb-item').hasClass('bcdistrict')) {
        $('.breadcrumb').find('.breadcrumb-item').remove('.bcdistrict');
    }

    if (!$('.breadcrumb').find('.breadcrumb-item').hasClass('bcregency')) {

        $('.breadcrumb').find('.breadcrumb-item').removeClass('active');
        $('.breadcrumb').append(
            '<li class="bcregency breadcrumb-item active"  onclick="openTblRegency(null , ' + "'" + name + "'" +
            ')" aria-current="page"><a href="#">Kota Kabupaten</a></li>'
        );
    }

    $('#table_title').html('PROVINSI ' + name);
    $('#table_subtitle').html('Menampilkan data - data Provinsi ' + name);

    $('#tbl_prov_container').addClass('d-none');
    $('#tbl_regency_container').removeClass('d-none');
    $('#tbl_district_container').addClass('d-none');

    if (mekop_regency_table != null) {
        mekop_regency_table.fnDestroy();
    }

    mekop_regency_table = $('.mekop_regency_table').dataTable({
        initComplete: function() {
            var input = $('#' + $(".mekop_regency_table").attr('id') + '_filter input').unbind(),
                self = this.api(),
                searchButton = $('<button>')
                .html('<i class="fas fa-search"></i>')
                .addClass('btn btn-outline-info btn-sm mx-1')
                .click(function() {
                    self.search(input.val()).draw();
                });

            $('#' + $(".mekop_regency_table").attr('id') + '_filter').append(searchButton);
        },
        "processing": true,
        "responsive": true,
        "serverSide": true,
        "pageLength": 20,

        "ajax": {
            "url": "{{ route('getallstatpkbregency.pkb') }}",
            "type": "POST",
            "headers": {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            "data": function(data) {
                data.province_id = id != null ? id : $('select[name="province_id"]').val();
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
                "width": "15%",
                "className": "text-center",
            },
            {
                "targets": 4,
                "width": "8%",
                "className": "text-center",
            },
            {
                "targets": 5,
                "width": "8%",
                "className": "text-center",
            },
            {
                "targets": 6,
                "width": "8%",
                "className": "text-center",
            },
            {
                "targets": 7,
                "width": "10%",
                "className": "text-center",
            },
            {
                "targets": 8,
                "width": "10%",
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

function openTblDistrict(id, name) {

    if (!$('.breadcrumb').find('.breadcrumb-item').hasClass('bcdistrict')) {
        $('.breadcrumb').find('.breadcrumb-item').removeClass('active');
        $('.breadcrumb').append(
            '<li class="bcdistrict breadcrumb-item active"  onclick="openTblDistrict(null , ' + "'" + name + "'" +
            ')" aria-current="page"><a href="#">Kecamatan</a></li>'
        );
    }

    $('#table_title').html(name);
    $('#table_subtitle').html('Menampilkan data - data ' + name);

    $('#tbl_prov_container').addClass('d-none');
    $('#tbl_regency_container').addClass('d-none');
    $('#tbl_district_container').removeClass('d-none');

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
            "url": "{{ route('getallstatpkbdistrict.pkb') }}",
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
                "width": "15%",
                "className": "text-center",
            },
            {
                "targets": 4,
                "width": "8%",
                "className": "text-center",
            },
            {
                "targets": 5,
                "width": "8%",
                "className": "text-center",
            },
            {
                "targets": 6,
                "width": "8%",
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