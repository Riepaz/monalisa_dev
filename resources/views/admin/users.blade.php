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
                            <h3 class="card-title">Pengguna
                            </h3>

                        </div>

                        <div class="card-body">

                            <div class="row mb-3">
                                <div class="card bg-gradient-indigo p-3 col-lg-6 col-12">
                                    <div class="row">
                                        <div class="col-lg-1 col-12">
                                            <h3><i class="fas fa-info m-2"></i></h3>
                                        </div>
                                        <div class="col-lg-11 col-12">
                                            <small class="text-white p_proper-screen_sm">Data pada
                                                pengguna dapat dimodifikasi, diantaranya; menonaktifkan dan
                                                mengaktifakan, mengubah <i>Role</i> dan Hak Akses Pengguna.</small>
                                            <hr>
                                            <small class="text-white p_proper-screen_sm">Data Pengguna
                                                adalah
                                                data yang pernah melakukan <b>Login</b> terhadap Sistem.</small>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <a class="btn btn-outline-info btn-sm my-3" href="{{ route('registerform') }}">Daftarkan</a>
                            <table class="table table-responsive table-sm table-bordered userstable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>NIP</th>
                                        <th>Nama</th>
                                        <th>Provinsi</th>
                                        <th>Daerah</th>
                                        <th>Hak Akses</th>
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
<div class="modal fade " tabindex="-1" role="dialog" id="compose_user_modal">
    <div class="modal-dialog modal-md card card-outline card-info" role="document">
        <div class="modal-content ">

            <form id="user_mod_form" name="user_mod_form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex p-4">
                    <div class="col-lg-12 col-12 pb-3">
                        <h3><b><i class="fas fa-user-cog"></i> Modifikasi Pengguna</b></h3>
                        <p id="title_course"></p>
                        <hr>

                        <div class="card bg-cyan p-3">
                            <p class="text-white p_proper-screen_md"><i>Role</i>
                                Pengguna adalah penanda sebagai batasan dari Hak Akses Pengguna.</p>
                            <small class="blockquote-footer text-white p_proper-screen_md">Superadmin, Administrator,
                                Widyaiswara, Partisipan / Peserta
                                .</small>
                        </div>

                        <hr>
                        <input id="mod_user_id" name="mod_user_id" type="text" hidden readonly>

                        <small for="role_id"><i>Role : </i></small>
                        <select id="role_id" name="role_id" class="form-control form-control-sm">
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
var userstable;
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

    userstable = $(".userstable").DataTable({
        initComplete: function() {
            var input = $('#' + $(".userstable").attr('id') + '_filter input').unbind(),
                self = this.api(),
                $searchButton = $('<button>')
                .html('<i class="fas fa-search"></i>')
                .addClass('btn btn-outline-info btn-sm mx-1')
                .click(function() {
                    self.search(input.val()).draw();
                });

            $('#' + $(".userstable").attr('id') + '_filter').append($searchButton);
        },
        "processing": true,
        "responsive": true,
        "serverSide": true,
        "pageLength": 20,
        "ajax": {
            "url": "{{ route('admin.getallusers') }}",
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
                "width": "8%",
                "className": "text-left",
            },
            {
                "targets": 2,
                "width": "10%",
                "className": "text-left",
            },
            {
                "targets": 3,
                "width": "10%",
                "className": "text-left p_proper-screen_sm",
            },
            {
                "targets": 4,
                "width": "10%",
                "className": "text-left p_proper-screen_sm",
            },
            {
                "targets": 5,
                "width": "5%",
                "className": "text-center p_proper-screen_sm",
            },
            {
                "targets": 6,
                "width": "5%",
                "className": "text-center",
            },
            {
                "targets": 7,
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

    $('#user_mod_form').submit(function(e) {
        if ($('#user_mod_form').valid()) {
            e.preventDefault();
            $('#btn_submit').html(
                '<i class="fas fa-spinner fa-spin text-white"></i> Proses...');
            $('#btn_submit').attr('disabled', true);

            var url = "{{ route('admin.submitmoduser') }}";

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
                        $('#user_mod_form')[0].reset();
                        $('#btn_submit').text('Simpan');
                        $('#btn_submit').attr('disabled', false);

                        $('#compose_user_modal').modal('hide');
                    }, 1000);

                    userstable.ajax.reload(null, false);

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
        url: "{{ route('panel.getallrole') }}",
        type: "get",
        processData: false,
        contentType: false,
        cache: false,

        success: function(data) {
            jQuery.each(data, function(key, value) {
                $('select[name="role_id"]').append('<option value="' +
                    key + '">' + value + '</option>');
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
});

$('#compose_new').click(function() {
    $('#mod_user_id').val('');
});

function edit_user(id) {
    $('#mod_user_id').val(id);

    $.ajax({
        url: "{{ route('admin.userbyid' , '') }}/" + id,
        type: "get",
        processData: false,
        contentType: false,
        cache: false,

        success: function(data) {
            $('#role_id').val(data[0].role_id);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}

function publish_user(id, status) {
    if (confirm('Yakin dengan Pengguna ini?')) {
        $.ajax({
            url: "{{ route('admin.activateuser') }}",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                id: id,
                status: status
            },

            success: function(data) {
                userstable.ajax.reload(null, false);
            },

            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }
}

function reset_password(id) {
    if (confirm('Reset Pengguna ini?')) {
        $.ajax({
            url: "{{ route('admin.resetpassword') }}",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                id: id,
                status: status
            },

            success: function(data) {
                userstable.ajax.reload(null, false);
                $(document).Toasts('create', {
                    title: 'Berhasil',
                    autohide: true,
                    autoremove: true,
                    delay: 1000,
                    body: 'Akun Berhasil setel ulang.'
                })

            },

            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }
}

function delete_user(id, status) {
    if (confirm('Yakin dengan Pengguna ini?')) {
        $.ajax({
            url: "{{ route('admin.deleteuser') }}",
            type: "post",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                status: status,
            },

            success: function(data) {
                userstable.ajax.reload(null, false);
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