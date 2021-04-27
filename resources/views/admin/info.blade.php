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
                            <h3 class="card-title">Informasi
                            </h3>

                        </div>

                        <div class="card-body">
                            <div class="row">
                                <button id="compose_new" name="compose_new" class="btn btn-outline-info btn-sm mb-3"
                                    data-toggle="modal" data-target="#compose_info_modal">
                                    Tambah
                                </button>
                            </div>

                            <table class="table table-responsive table-bordered infotable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Informasi</th>
                                        <th>Kategori</th>

                                        @if(Auth::user()->hasRole('adminprovinsi'))
                                        <th>Views</th>
                                        @elseif(Auth::user()->hasRole('superadmin') or Auth::user()->hasRole('admin'))
                                        <th>Wilayah</th>
                                        @endif

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
<div class="modal fade " tabindex="-1" role="dialog" id="compose_info_modal">
    <div class="modal-dialog modal-xl card card-outline card-info" role="document">
        <div class="modal-content ">

            <form id="info_form" name="info_form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex p-4">
                    <div class="col-lg-4 col-12 pb-3">
                        <h3><b><i class="fas fa-info mr-2"></i>Informasi Utama</b></h3>
                        <p id="title_course"></p>
                        <hr>

                        <div class="card bg-cyan p-3">
                            <small class="quote">Informasi akan dapat dilihat siapa saja dan dapat
                                dibagikan oleh pengguna secara umum.</small>
                            <hr>
                            <small class="blockquote-footer text-white p_proper-screen_md">Aktifasi dapat dilakukan
                                setelah
                                menyimpan.</small>
                            <small class="blockquote-footer text-white p_proper-screen_md">Anda dapat mengatifkan
                                Informasi
                                pada tanggal
                                publikasi.</small>
                            <small class="blockquote-footer text-white p_proper-screen_md">Jangan Lupa untuk selalu
                                memberikan referensi
                                sumber.</small>

                        </div>

                        <div class="row">
                            <button id="btn_submit" class="btn btn-success btn-sm m-1" type="submit">Simpan</button>
                            <button class="btn btn-danger btn-sm m-1" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>


                    <div class="col-lg-8 col-12 align-items-center justify-content-center">
                        <div class="form-group">


                            <input id="info_id" name="info_id" type="text" hidden readonly>

                            <input id="info_title" name="info_title" class="form-control form-control-sm "
                                placeholder="Judul Informasi" required></input>

                            <select name="category" class=" form-control form-control-sm mt-2" id="category">
                            </select>

                            <select name="province_id" class=" form-control form-control-sm mt-2" id="province_id">
                            </select>

                            <select name="activation" class=" form-control form-control-sm mt-2" id="activation">
                                <option value="0" selected>Inactive</option>
                                <option value="1">Active</option>
                            </select>

                        </div>
                        <div class="form-group w-100 h-100">
                            <textarea id="compose_overview_info" name="compose_overview_info"
                                class="form-control form-control-sm " required></textarea>
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
var infotable;
var Participanttable;


$(document).ready(function() {
    $('#compose_overview_info').summernote({
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
        placeholder: 'Buat Informasi Di sini...',

        height: 200,
        focus: false
    });

    infotable = $(".infotable").DataTable({
        initComplete: function() {

            var input = $('#DataTables_Table_0_filter input').unbind(),
                self = this.api(),
                $searchButton = $('<button>')
                .html('<i class="fas fa-search"></i>')
                .addClass('btn btn-outline-info btn-sm mx-1')
                .click(function() {
                    self.search(input.val()).draw();
                });

            $('#DataTables_Table_0_filter').append($searchButton);
        },
        "processing": true,
        "responsive": true,
        "serverSide": true,
        "pageLength": 20,
        "ajax": {
            "url": "{{ route('admin.getallinfo') }}",
            "type": "POST",
            "headers": {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
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
                "width": "25%",
                "className": "text-left",
            },
            {
                "targets": 2,
                "width": "10%",
                "className": "text-center",
            },
            {
                "targets": 3,
                "width": "13%",
                "className": "text-center",
            },
            {
                "targets": 4,
                "width": "10%",
                "className": "text-center",
            },
            {
                "targets": 5,
                "width": "15%",
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

    $('#info_form').submit(function(e) {
        if ($('#info_form').valid()) {
            e.preventDefault();
            $('#btn_submit').html(
                '<i class="fas fa-spinner fa-spin text-white"></i> Proses...');
            $('#btn_submit').attr('disabled', true);

            var url = "{{ route('admin.submitinfo') }}";

            $.ajax({
                url: url,
                type: "post",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,

                success: function(data) {
                    $('#btn_submit').html(
                        '<i class="fas fa-check text-white"></i>');

                    setTimeout(function() {
                        $('#info_form')[0].reset();
                        $('#btn_submit').text('Simpan');
                        $('#btn_submit').attr('disabled', false);

                        $('#compose_info_modal').modal('hide');
                    }, 1000);

                    infotable.ajax.reload(null, false);

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
        url: "{{ route('admin.infocategories') }}",
        type: "GET",
        dataType: "json",
        success: function(data) {
            $('select[name="category"]').empty();
            $.each(data, function(key, value) {
                $('select[name="category"]').append('<option value="' +
                    key + '">' + value + '</option>');
            });
        }
    });

    var provincesUrl;
    if ("{{ Auth::user()->hasRole('adminprovinsi') }}") {
        provincesUrl = "{{ route('provinces.getbyauth') }}";
    } else {
        provincesUrl = "{{ route('provinces.get') }}";
    }

    $.ajax({
        url: provincesUrl,
        type: "GET",
        dataType: "json",
        success: function(data) {
            $('select[name="province_id"]').empty();
            $.each(data, function(key, value) {
                $('select[name="province_id"]').append('<option value="' + key + '">' +
                    value + '</option>');
            });
        }
    });
});

$('#compose_new').click(function() {
    $('#info_form')[0].reset();
    $('#info_id').val('');
    $('#compose_overview_info').summernote('code', '');
    $('#activation').attr('disabled', true);
});

function edit_info(id) {
    $('#info_id').val(id);
    $('#activation').attr('disabled', false);

    $.ajax({
        url: "{{ route('admin.infobyid' , '') }}/" + id,
        type: "get",
        processData: false,
        contentType: false,
        cache: false,

        success: function(data) {
            $('#info_title').val(data[0].title);
            $('#category').val(data[0].category_id);
            $('#province_id').val(data[0].province_id);
            $('#activation').val(data[0].is_active);
            $('#compose_overview_info').summernote('code', data[0].overview);

        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}

function publish_info(id, status) {
    if (confirm('Yakin dengan Informasi ini?')) {
        $.ajax({
            url: "{{ route('admin.activateinfo') }}",
            type: "post",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                status: status,
            },

            success: function(data) {
                infotable.ajax.reload(null, false);
            },

            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }
}

function delete_info(id) {
    if (confirm('Yakin dengan Informasi ini?')) {
        $.ajax({
            url: "{{ route('admin.deleteinfo','') }}/" + id,
            type: "get",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                status: status,
            },

            success: function(data) {
                infotable.ajax.reload(null, false);
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