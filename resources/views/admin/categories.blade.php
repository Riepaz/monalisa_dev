@extends('layouts.backend.backend-template')

@section('content')
<style>
.note-editable {
    max-width: 794px;
    height: 1123px;
}

@media screen and (min-width: 992px) {

    .note-editable {
        min-width: 600px;
    }

}
</style>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <div class="content-wrapper pt-5">
            <section class="content">
                <h6 id="preloader" class=" text-center">
                    <i class="fas fa-spinner fa-spin"></i>
                </h6>

                <div id="container" class="container-fluid" hidden>

                    <!-- Default box -->
                    <div class="card card-outline card-primary ">
                        <div class="card-header d-flex">
                            <h3 class="card-title">Kategori
                            </h3>

                        </div>

                        <div class="card-body">

                            <div class="row">
                                <button id="compose_new" name="compose_new" class="btn btn-outline-info btn-sm mb-3"
                                    data-toggle="modal" data-target="#compose_category_modal">
                                    Tambah
                                </button>
                            </div>

                            <table class="table table-responsive table-hover table-bordered categorytable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Kategori</th>
                                        <th>Tahun Anggaran</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                    </div>
                    <!-- /.card -->


                </div>
            </section>
        </div>
</body>



<!--MODAL DETAIL-->
<div class="modal fade " tabindex="-1" role="dialog" id="compose_category_modal">
    <div class="modal-dialog modal-md card card-outline card-info" role="document">
        <div class="modal-content ">

            <form id="category_form" name="category_form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex p-4">
                    <div class="col-lg-12 col-12 pb-3">
                        <h3><b><i class="fas fa-shapes mr-2"></i>Kategori</b></h3>
                        <p id="title_course"></p>
                        <hr>

                        <div class="card bg-cyan p-3">
                            <small class="blockquote-footer text-white p_proper-screen_md">Sesuaikan dengan Kebutuhan
                                Kategori Mekop / Indikator Mekop.</small>

                        </div>

                        <hr>
                        <input id="category_id" name="category_id" type="text" hidden readonly>

                        <input id="category_names" name="category_names" class="form-control form-control-sm "
                            placeholder="Nama Kategori" required></input>

                        <small class="text-dark" for="fiscaly_id">Tahun Anggaran</small>
                        <select id="fiscaly_id" name="fiscaly_id" class="form-control form-control-sm mt-1">
                        </select>

                        <small class="text-dark" for="level_id">Tingkat</small>
                        <select id="level_id" name="level_id" class="form-control form-control-sm mt-1">
                            <option value="3">Kecamatan</option>
                            <option value="4">Kelurahan & Desa</option>
                        </select>


                        <hr>

                        <div class="row">
                            <button id="btn_submit" class="btn btn-success btn-sm m-1" type="submit">Simpan</button>
                            <button class="btn btn-danger btn-sm m-1" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection


@section('javascript')

<script type="text/javascript">
var bima_api_url = $('#bima_url').val();
var bima_token = $('#bima_token').val();
var categorytable;
var Participanttable;


$(document).ready(function() {
    $('#compose_overview_news').summernote({
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['fontsize', 'color']],
            ['font', ['fontname']],
            ['para', ['paragraph']],
            ['insert', ['link', 'image', 'doc', 'video']],
            ['misc', ['codeview', 'fullscreen']],
            ['fontsize', ['fontsize']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['picture', 'hr']],
            ['table', ['table']]
        ],
        lineHeights: ['0.2', '0.3', '0.4', '0.5', '0.6', '0.8', '1.0', '1.2', '1.4', '1.5', '2.0',
            '3.0'
        ],

        styleTags: [
            'p',
            {
                title: 'Blockquote',
                tag: 'blockquote',
                className: 'blockquote',
                value: 'blockquote'
            },
            'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
        ],
        placeholder: 'Buat Berita Di sini...',

        height: 200,
        focus: false
    });

    categorytable = $(".categorytable").DataTable({
        "processing": true,
        "responsive": true,
        "ajax": {
            "url": "{{ route('admin.getallcategories') }}",
            "type": "GET",
            "dataType": "json",
            "headers": {
                "Content-Type": "application/json",
                "accept": "*/json",
            },
        },
        "order": [
            [0, "asc"]
        ],
        'columnDefs': [{
                "targets": 0,
                "width": "2%",
                "className": "text-center",
            },
            {
                "targets": 1,
                "width": "30%",
                "className": "text-left",
            },
            {
                "targets": 2,
                "width": "5%",
                "className": "text-center",
            },
            {
                "targets": 3,
                "width": "8%",
                "className": "text-center",
            },
            {
                "targets": 4,
                "width": "8%",
                "className": "text-center",
            },
            {
                "targets": 5,
                "width": "10%",
                "className": "text-center",
            },

        ],

        "language": {
            "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
            "sProcessing": "<i class='fas fa-spinner fa-spin text-success'></i>  Sedang memproses...",
            "sLengthMenu": "Tampilkan _MENU_ entri",
            "sZeroRecords": "Tidak ditemukan data yang sesuai",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix": "",
            "sSearch": "Cari:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext": "Selanjutnya",
                "sLast": "Terakhir"
            }
        },
    });

    $('#category_form').submit(function(e) {
        if ($('#category_form').valid()) {
            e.preventDefault();
            $('#btn_submit').html(
                '<i class="fas fa-spinner fa-spin text-white"></i> Proses...');
            $('#btn_submit').attr('disabled', true);

            var url = "{{ route('admin.submitcategory') }}";

            $.ajax({
                url: url,
                type: "post",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,

                success: function(data) {

                    console.log(data);

                    $('#btn_submit').html(
                        '<i class="fas fa-check text-white"></i>');

                    setTimeout(function() {
                        $('#category_form')[0].reset();
                        $('#btn_submit').text('Simpan');
                        $('#btn_submit').attr('disabled', false);

                        $('#compose_category_modal').modal('hide');
                    }, 1000);

                    categorytable.ajax.reload(null, false);

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('#btn_submit').text('Simpan');
                    $('#btn_submit').attr('disabled', false);
                }
            });
        }
    });

    $.ajax({
        url: "{{ route('admin.getallfiscalyearsoption') }}",
        type: "get",
        processData: false,
        contentType: false,
        cache: false,

        success: function(data) {
            jQuery.each(data, function(key, value) {
                $('select[name="fiscaly_id"]').append('<option value="' +
                    key + '">' + value + '</option>');
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });

});

$('#compose_new').click(function() {
    $('#category_form')[0].reset();
    $('#category_id').val('');
    $('#category_names').val('');
    $("#fiscaly_id option:last").attr("selected", "selected");
});

function edit_category(id) {
    $('#category_id').val(id);

    $.ajax({
        url: "{{ route('admin.categorybyid' , '') }}/" + id,
        type: "get",
        processData: false,
        contentType: false,
        cache: false,

        success: function(data) {
            $('#category_names').val(data[0].mekop);
            $('#fiscaly_id').val(data[0].tahun_id);
            $('#level_id').val(data[0].tingkat);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}

function delete_category(id) {
    if (confirm('Yakin dengan Kategori ini?')) {
        $.ajax({
            url: "{{ route('admin.deletecategory','') }}/" + id,
            type: "get",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                status: status,
            },

            success: function(data) {
                categorytable.ajax.reload(null, false);
            },

            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }
}

function publish_category(id, status) {
    if (confirm('Yakin dengan Kategori ini?')) {
        $.ajax({
            url: "{{ route('admin.activatecategory') }}",
            type: "post",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                status: status,
            },

            success: function(data) {
                categorytable.ajax.reload(null, false);
            },

            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }
}

$(document).ready(function() {
    $('#preloader').html('')
    $('#container').attr('hidden', false);
});
</script>
@endsection