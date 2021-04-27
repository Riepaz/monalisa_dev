@extends('layouts.backend.backend-template')

@section('content')

<!-- LEGAL PAPER SIZE -->
<style>
.note-editable {
    max-width: 794px;
    max-height: 1123px;
}

#view_detail {
    max-width: 794px;
    max-height: 1123px;
}

@media screen and (min-width: 992px) {
    #view_detail {
        min-width: 600px;
    }

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
                            <h3 class="card-title">Sertifikat
                            </h3>

                        </div>
                        <div class="card-body">
                            <div class="d-flex mb-2">


                            </div>
                            <table class="table table-responsive table-bordered certtable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Kelas</th>
                                        <th>Provider</th>
                                        <th>Peserta</th>
                                        <th>view</th>
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
<div class="modal fade " tabindex="-1" role="dialog" id="compose_cerfificates_modal">
    <div class="modal-dialog modal-xl card card-outline card-info" role="document">
        <div class="modal-content ">

            <form id="cert_form" name="cert_form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex p-4">
                    <div class="col-lg-4 col-12 pb-3">
                        <h3><b><i class="fas fa-medal"></i>Template Sertifikat</b></h3>
                        <p id="title_course"></p>
                        <hr>

                        <div class="card bg-cyan p-3">
                            <small class="quote">Gunakan tombol - tombol diatas untuk menambahkan pengubah untuk nilai
                                sebenarnya.</small>
                            <hr>
                            <small class="quote">Sesuaikan pengubah pada posisi di dalam template Sertifikat.</small>

                        </div>
                        <div class="row">
                            <label onclick="insert_txt('{cert_num}')" class="btn btn-outline-primary btn-sm m-1">No
                                Sertifikat</label>
                            <label onclick="insert_txt('{event_date}')"
                                class="btn btn-outline-primary btn-sm m-1">Tanggal
                                Pelatihan</label>
                            <label onclick="insert_txt('{validation_date}')"
                                class="btn btn-outline-primary btn-sm m-1">Tanggal
                                Disahkan</label>
                            <label onclick="insert_txt('{participant_name}')"
                                class="btn btn-outline-primary btn-sm m-1">Nama
                                Peserta</label>
                            <label onclick="insert_txt('{score}')" class="btn btn-outline-primary btn-sm m-1">Skor
                                Peserta</label>
                        </div>

                        <hr>
                        <div class="row">
                            <button id="btn_submit_course" class="btn btn-success btn-sm m-1"
                                type="submit">Simpan</button>
                            <button class="btn btn-danger btn-sm m-1" data-dismiss="modal">Tutup</button>
                        </div>

                    </div>

                    <div class="col-lg-8 col-12 align-items-center justify-content-center">
                        <div class="form-group">

                            <input id="course_id" name="course_id" type="text" hidden readonly>
                            <input id="certificate_name" name="certificate_name" class="form-control form-control-sm "
                                placeholder="Nama Sertifikat" required></input>
                        </div>
                        <div class="form-group w-100 h-100">
                            <textarea id="compose_certificate" name="compose_certificate"
                                class="form-control form-control-sm " required></textarea>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<!--MODAL DETAIL-->
<div class="modal fade " tabindex="-1" role="dialog" id="release_cerfificates_modal">
    <div class="modal-dialog modal-xl card card-outline card-info" role="document">
        <div class="modal-content ">

            <form id="cert_form" name="cert_form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex p-4">
                    <div class="col-lg-4 col-12 pb-3">
                        <h3><b><i class="fas fa-medal"></i> Penganugerahan Sertifikat</b></h3>
                        <p id="title_course"></p>
                        <hr>

                        <div class="card bg-warning p-3">
                            <div class="row d-flex align-items-center justify-content-center">
                                <div class="col-2">
                                    <h1><i class="fas fa-info pl-3"></i></h1>
                                </div>
                                <div class="col-10">
                                    <small class="quote">Dalam satu kelas pelatihan akan ada beberapa peserta yang
                                        tidak lulus atau tidak sesuai dengan standar kelulusan peserta, namun dalam
                                        sistem tidak akan dibatasi untuk penganugerahan sertifikat.</small>
                                </div>
                            </div>
                            <hr>
                            <small class="quote"><b>Sesuaikan penganugerahan sertifikat dengan standar kelulusan
                                    peserta.</b></small>

                        </div>

                        <hr>
                        <div class="row">
                            <button class="btn btn-danger btn-sm m-1" data-dismiss="modal">Tutup</button>
                        </div>

                    </div>

                    <div class="col-lg-8 col-12 align-items-center justify-content-center">

                        <div class="card col-12 p-4">
                            <table class="table table-responsive table-bordered participanttable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Progress</th>
                                        <th>Skor</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<!--MODAL DETAIL-->
<div class="modal fade " tabindex="-1" role="dialog" id="view_cerfificates_modal">
    <div class="modal-dialog modal-lg card card-outline card-info" role="document">
        <div class="modal-content p-3">
            <div class="modal-header ">
                <a id="linkview_pdf" target="_blank" class="btn btn-outline-primary">Lihat sebagai
                    PDF</a>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id="view_detail" class="mx-3 my-5">

            </div>

        </div>
    </div>
</div>

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
var bima_api_url = $('#bima_url').val();
var bima_token = $('#bima_token').val();
var Certtable;
var Participanttable;


$(document).ready(function() {
    $('#compose_certificate').summernote({
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
        placeholder: 'Buat Template Sertifikat Di sini...',

        height: 200,
        focus: false
    });

    Certtable = $(".certtable").DataTable({
        "processing": true,
        "responsive": true,
        "ajax": {
            "url": "{{ route('admin.mycertificates') }}",
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
                "width": "13%",
                "className": "text-center",
            },
            {
                "targets": 5,
                "width": "10%",
                "className": "text-center",
            },
            {
                "targets": 6,
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

    $('#cert_form').submit(function(e) {
        if ($('#cert_form').valid()) {
            e.preventDefault();
            $('#btn_submit_course').html(
                '<i class="fas fa-spinner fa-spin text-white"></i> Proses...');
            $('#btn_submit_course').attr('disabled', true);

            var url = "{{ route('admin.submitcertificates') }}";

            $.ajax({
                url: url,
                type: "post",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,

                success: function(data) {
                    $('#btn_submit_course').html(
                        '<i class="fas fa-check text-white"></i>');

                    setTimeout(function() {
                        $('#compose_cerfificates_modal').modal('hide');

                        $('#cert_form')[0].reset();
                        $('#btn_submit_course').text('Simpan');
                        $('#btn_submit_course').attr('disabled', false);
                    }, 1000);



                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('#btn_submit_course').text('Simpan');
                    $('#btn_submit_course').attr('disabled', false);
                }
            });
        }
    });
});

function activationclass(id) {
    if (confirm('Anda yakin ?')) {
        $.ajax({
            url: "{{ route('admin.activationclass' , '') }}?id=" + id,
            type: "GET",
            dataType: "json",
            headers: {
                "Content-Type": "application/json",
                "accept": "*/json",
            },
            success: function(result) {
                Certtable.ajax.reload(null, false);
            }
        });
    }
}

function create_crt(id) {
    $('#cert_form')[0].reset();
    $('#compose_certificate').summernote('code', '');
    $('#course_id').val(id);

    $.ajax({
        url: "{{ route('admin.getcert' , '') }}/" + id,
        type: "GET",
        dataType: "json",
        headers: {
            "Content-Type": "application/json",
            "accept": "*/json",
        },
        success: function(result) {
            if (result !== null) {
                $('#certificate_name').val(result['name']);
                $('#compose_certificate').summernote('code', result['template']);
            }
        }
    });
}

function view_crt(id) {
    $('#view_detail').html('');
    $('#linkview_pdf').attr('href', "{{ route('admin.getcertview' , '') }}/" + id)

    $.ajax({
        url: "{{ route('admin.getcert' , '') }}/" + id,
        type: "GET",
        dataType: "json",
        headers: {
            "Content-Type": "application/json",
            "accept": "*/json",
        },
        success: function(result) {
            if (result !== null) {
                $('#view_detail').html(result['template']);
            } else {
                $('#view_detail').html('Belum Ada Template...');
            }
        }
    });
}

function create_user_crt(user_id, email, reg_number, course_id, course_slug, provider) {
    if (confirm('Apakah Pilihan Sudah Benar ?')) {
        $('.givecert').html('<i class="fas fa-spinner fa-spin"></i>');
        $.ajax({
            url: "{{ route('admin.releasecert') }}",
            type: "post",
            data: {
                _token: "{{ csrf_token() }}",
                user_id: user_id,
                user_email: email,
                user_reg_number: reg_number,
                course_id: course_id,
                slug: course_slug,
                provider: provider
            },
            success: function(result) {
                if (result) {
                    var obj = JSON.parse(result);
                    console.log(obj.error);
                }

                Participanttable.ajax.reload(null, false);
            }
        });
    }
}

function release_crt(id) {

    $('#release_cerfificates_modal').modal('show');

    $(".participanttable").dataTable().fnDestroy();

    Participanttable = $(".participanttable").DataTable({
        "processing": true,
        "responsive": true,
        "ajax": {
            "url": "{{ route('admin.getparticipantscourses' , '') }}/" + id,
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
                "width": "15%",
                "className": "text-center",
            },
            {
                "targets": 4,
                "width": "3%",
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

}

function insert_txt(text) {
    $('#compose_certificate').summernote('editor.insertText', text);
}

$(document).ready(function() {
    $('#preloader').html('')
    $('#container').attr('hidden', false);
});
</script>
@endsection