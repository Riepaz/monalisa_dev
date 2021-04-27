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
                            <h3 class="card-title">Berita
                            </h3>

                        </div>

                        <div class="card-body">

                            <div class="row">
                                <button id="compose_new" name="compose_new" class="btn btn-outline-info btn-sm mb-3"
                                    data-toggle="modal" data-target="#compose_news_modal">
                                    Tambah
                                </button>
                            </div>

                            <table class="table table-responsive table-bordered newstable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Headline</th>
                                        <th>Kategori</th>
                                        <th>Views</th>
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
<div class="modal fade " tabindex="-1" role="dialog" id="compose_news_modal">
    <div class="modal-dialog modal-xl card card-outline card-info" role="document">
        <div class="modal-content ">

            <form id="news_form" name="news_form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex p-4">
                    <div class="col-lg-4 col-12 pb-3">
                        <h3><b><i class="fas fa-blog mr-2"></i>Berita Terkini</b></h3>
                        <p id="title_course"></p>
                        <hr>

                        <div class="card bg-cyan p-3">
                            <small class="quote">Berita akan dapat dilihat siapa saja dan dapat
                                dibagikan oleh pengguna secara umum.</small>
                            <hr>
                            <small class="blockquote-footer text-white p_proper-screen_md">Aktifasi dapat dilakukan
                                setelah
                                menyimpan.</small>
                            <small class="blockquote-footer text-white p_proper-screen_md">Anda dapat mengatifkan berita
                                pada tanggal
                                publikasi.</small>
                            <small class="blockquote-footer text-white p_proper-screen_md">Jangan Lupa untuk selalu
                                memberikan referensi
                                sumber.</small>

                        </div>

                        <div class="col-12 align-items-center justify-content-center">
                            <small id="lbl_error_photo" class="text-danger"></small>
                            <input type="file" id="photo_source" name="photo_source" hidden />
                            <input id="photo_base64" name="photo_base64" hidden required />
                            <label for="photo_source" type="button" class="btn btn-outline-info attach-image-button"
                                id="btn_photo_lbl"></img></label>
                        </div>

                        <hr>
                        <div class="row">
                            <button id="btn_submit" class="btn btn-success btn-sm m-1" type="submit">Simpan</button>
                            <button class="btn btn-danger btn-sm m-1" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>


                    <div class="col-lg-8 col-12 align-items-center justify-content-center">
                        <div class="form-group">


                            <input id="news_id" name="news_id" type="text" hidden readonly>

                            <input id="news_title" name="news_title" class="form-control form-control-sm "
                                placeholder="Judul Berita" required></input>

                            <select name="category" class=" form-control form-control-sm mt-2" id="category">
                            </select>

                            <select name="activation" class=" form-control form-control-sm mt-2" id="activation">
                                <option value="0" selected>Inactive</option>
                                <option value="1">Active</option>
                            </select>

                        </div>
                        <div class="form-group w-100 h-100">
                            <textarea id="compose_overview_news" name="compose_overview_news"
                                class="form-control form-control-sm " required></textarea>
                        </div>
                    </div>
                </div>
            </form>

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
var newstable;
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

    newstable = $(".newstable").DataTable({
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
            "url": "{{ route('admin.getallnews') }}",
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

    $('#news_form').submit(function(e) {
        e.preventDefault();
        $('#btn_submit').html(
            '<i class="fas fa-spinner fa-spin text-white"></i> Proses...');
        $('#btn_submit').attr('disabled', true);

        var url = "{{ route('admin.submitnews') }}";

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
                    $('#news_form')[0].reset();
                    $('#btn_submit').text('Simpan');
                    $('#btn_submit').attr('disabled', false);

                    $('#compose_news_modal').modal('hide');
                }, 1000);

                newstable.ajax.reload(null, false);

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
                $('#btn_submit').text('Simpan');
                $('#btn_submit').attr('disabled', false);
            }
        });
    });

    $.ajax({
        url: "{{ route('admin.newscategories') }}",
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
});

$('#compose_new').click(function() {
    $('#news_form')[0].reset();
    $('#news_id').val('');
    $('#compose_overview_news').summernote('code', '');
    $('#activation').attr('disabled', true);
    $('#btn_photo_lbl').html(
        "<img src='{{ asset('assets/img/logo/default_banner.png') }}' style='width:100%;'>");

    $('#btn_photo_lbl').removeClass('btn-outline-danger');
    $('#btn_photo_lbl').addClass('btn-outline-info');
    $('#lbl_error_photo').html('');
});


$('#btn_submit').on('click', function() {
    if ($('#photo_base64').val() == '') {
        $('#btn_photo_lbl').removeClass('btn-outline-info');
        $('#btn_photo_lbl').addClass('btn-outline-danger');
        $('#lbl_error_photo').html('* Lokasi Tidak Boleh Kosong');
    } else {
        $('#btn_photo_lbl').removeClass('btn-outline-danger');
        $('#btn_photo_lbl').addClass('btn-outline-info');
        $('#lbl_error_photo').html('');
    }
});

function edit_news(id) {
    $('#news_id').val(id);
    $('#activation').attr('disabled', false);

    $.ajax({
        url: "{{ route('admin.newsbyid' , '') }}/" + id,
        type: "get",
        processData: false,
        contentType: false,
        cache: false,

        success: function(data) {
            $('#news_title').val(data[0].title);
            $('#category').val(data[0].category_id);
            $('#activation').val(data[0].is_active);
            $('#compose_overview_news').summernote('code', data[0].overview);

            $('#photo_base64').val('true');

            $('#btn_photo_lbl').html("<img src='{{ asset('storage/news') }}/" + data[0].banner +
                "?nocahce=" + Date.now() + "' style='width:100%;'>");

        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}

function publish_news(id, status) {
    if (confirm('Yakin dengan berita ini?')) {
        $.ajax({
            url: "{{ route('admin.activatenews') }}",
            type: "post",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                status: status,
            },

            success: function(data) {
                newstable.ajax.reload(null, false);
            },

            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }
}

function delete_news(id) {
    if (confirm('Yakin dengan berita ini?')) {
        $.ajax({
            url: "{{ route('admin.deletenews','') }}/" + id,
            type: "get",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                status: status,
            },

            success: function(data) {
                newstable.ajax.reload(null, false);
            },

            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }
}


$("#photo_source").change(function() {
    photoIDforCrop = $('#btn_photo_lbl');
    photoIDFileforCrop = $('#photo_base64');

    var fileName, fileExtension, fileSize;


    fileName = document.getElementById('photo_source').files[0].name;
    fileSize = document.getElementById('photo_source').files[0].size;
    fileExtension = fileName.replace(/^.*\./, '');


    if (fileExtension == 'png' || fileExtension == 'jpg' || fileExtension == 'jpeg') {

        if (fileSize > 1 * 1024 * 1024) {
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Gambar Ditolak',
                subtitle: 'Ukuran Gambar terlalu besar',
                body: 'Ukuran Gambar melebihin 1 Mb.'
            });
        } else {
            var reader = new FileReader();
            reader.onload = function(event) {
                $image_crop.croppie('bind', {
                    url: event.target.result
                }).then(function() {

                });
            }
            reader.readAsDataURL(this.files[0]);
            $('#uploadimageModal').modal('show');
        }

    } else {
        $(document).Toasts('create', {
            class: 'bg-danger',
            title: 'Gambar Ditolak',
            subtitle: 'File tidak mendukung',
            body: 'Gunakan file dengan ekstensi .png/.jpg/.jpeg'
        });
    }
})

$image_crop = $('#image_preview').croppie({
    enableExif: true,
    viewport: {
        width: 500,
        height: 350,
        type: 'square' //circle
    },
    boundary: {
        width: 600,
        height: 450
    }
});

$('.crop_image').click(function(event) {

    $image_crop.croppie('result', {
        type: 'canvas',
        size: 'original',
        quality: 1,

    }).then(function(response) {
        if (response != '') {
            if (confirm('Yakin dengan foto ini?')) {
                photoIDFileforCrop.val(response);
                photoIDforCrop.html("<img src='" + response +
                    "'style='width:100%;'>");

                $('#uploadimageModal').modal('hide');
            }
        }
    })
});

$(document).ready(function() {
    $('#preloader').html('')
    $('#container').attr('hidden', false);
});
</script>
@endsection