@extends('layouts.frontend.frontend-template')


@section('content')
<!-- content start -->
<style>
.required-ast:after {
    content: " *";
    color: red;
}
</style>
<div class="container auth-container">

    <!-- account block start -->
    <div class="container  bg-light">
        <div class="row bg-white round my-3">
            <div class="row col-lg-12 col-12 p-4 m-0 ">

                <div class="col-lg-12 col-12">
                    <div class="rightRegisterForm w-100 my-2">
                        <form class="form-horizontal" method="POST" action="{{ route('register') }}" id="registerForm">
                            {{ csrf_field() }}
                            <div id="account_container" class="callout callout-info px-4 py-5">
                                <h3>Selamat Datang</h3>
                                <small>Mari bergabung bersama Monalisa.</small>
                                <hr>

                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="text-sm">Username <span class="required-ast">
                                                </span></label>
                                            <input required type="username" class="form-control form-control-sm"
                                                placeholder="Username" name="username" id="username">
                                            @if ($errors->has('username'))
                                            <p class="text-sm text-red" class="error" for="first_name">
                                                {{ $errors->first('username') }}</p>
                                            @endif
                                            <div id="username-feedback">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="text-sm">Email <span class="required-ast"></span></label>
                                            <input required type="text" class="form-control form-control-sm"
                                                placeholder="Email"
                                                value="@if(!empty($email)){{ $email }}@else{{ old('email') }}@endif"
                                                name="email" @if(!empty($email)) readonly @endif>
                                            @if ($errors->has('email'))
                                            <p class="text-sm text-red" class="error" for="first_name">
                                                {{ $errors->first('email') }}</p>
                                            @endif
                                            <div id="email-feedback">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="text-sm">Nomor WhatsApp <span
                                                    class="required-ast"></span></label>
                                            <input required type="phone" class="form-control form-control-sm"
                                                placeholder="Nomor WhatsApp" value="{{ old('phone') }}" name="phone">
                                            @if ($errors->has('phone'))
                                            <p class="text-sm text-red" class="error" for="first_name">
                                                {{ $errors->first('phone') }}</p>
                                            @endif
                                            <div id="phone-feedback">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="text-sm">Password <span class="required-ast">
                                                </span></label>
                                            <input required type="password" class="form-control form-control-sm"
                                                placeholder="Password" name="password" id="password">
                                            @if ($errors->has('password'))
                                            <p class="text-sm text-red" class="error" for="first_name">
                                                {{ $errors->first('password') }}</p>
                                            @endif
                                            <div id="password-feedback">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="text-sm">Konfirmasi Password <span
                                                    class="required-ast"></span></label>
                                            <input required type="password" class="form-control form-control-sm"
                                                placeholder="Konfirmasi Password" name="c_password">
                                            @if ($errors->has('c_password'))
                                            <p class="text-sm text-red" class="error" for="first_name">
                                                {{ $errors->first('c_password') }}</p>
                                            @endif
                                            <div id="c_password-feedback">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <input type="hidden" value="{{ $provider_id }}" class="form-control form-control-sm"
                                    placeholder="Provider ID" name="provider_id">

                                <input type="hidden" class="form-control form-control-sm" value="{{ $provider }}"
                                    placeholder="Provider ID" name="provider">

                                <div id="nav_account" class="row">
                                    <a href="#" class="text-black" onclick="account_process()">Selanjutnya <i
                                            class="fas fa-arrow-right"></i></a>
                                </div>

                            </div>

                            <div id="owndata_container" class="callout callout-info px-4 py-5 d-none">
                                <h4><i class="fas fa-user-circle mr-2"></i>Data Pribadi</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-6 col-12">

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 mt-2">
                                                    <label class="text-sm">Nama Awal <span
                                                            class="required-ast"></span></label>
                                                    <input required type="text" class="form-control form-control-sm"
                                                        placeholder="Nama Awal"
                                                        value="@if(!empty($name)){{ $name }}@else{{ old('first_name') }}@endif"
                                                        name="first_name">
                                                    @if ($errors->has('first_name'))
                                                    <p class="text-sm text-red" class="error" for="first_name">
                                                        {{ $errors->first('first_name') }}</p>
                                                    @endif
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <label class="text-sm">Nama Akhir</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="Nama Akhir" value="{{ old('last_name') }}"
                                                        name="last_name">
                                                    @if ($errors->has('last_name'))
                                                    <p class="text-sm text-red" class="error" for="first_name">
                                                        {{ $errors->first('last_name') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="text-sm">Tempat Lahir <span
                                                    class="required-ast"></span></label>
                                            <input required type="text" class="form-control form-control-sm"
                                                placeholder="Tempat Lahir" id="birth_place" name="birth_place">
                                            @if ($errors->has('birth_place'))
                                            <p class="text-sm text-red" class="error" for="first_name">
                                                {{ $errors->first('birth_place') }}</p>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label class="text-sm">Tanggal Lahir <span
                                                    class="required-ast"></span></label>
                                            <input required type="date" class="form-control form-control-sm"
                                                placeholder="Tempat Lahir" id="birth_date" name="birth_date">
                                            @if ($errors->has('birth_date'))
                                            <p class="text-sm text-red" class="error" for="first_name">
                                                {{ $errors->first('birth_date') }}</p>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label class="text-sm">Usia <span class="required-ast"></span></label>
                                            <input required type="number" class="form-control form-control-sm"
                                                placeholder="Usia" value="{{ old('age') }}" name="age">
                                            @if ($errors->has('age'))
                                            <p class="text-sm text-red" class="error" for="first_name">
                                                {{ $errors->first('age') }}</p>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label class="text-sm" class="text-sm" for="gender">Jenis Kelamin : <span
                                                    class="required-ast text-red">
                                                </span></label>
                                            <select required name="gender" id="gender"
                                                class=" form-control form-control-sm">
                                                <option value="male">Laki - Laki</option>
                                                <option value="female">Perempuan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-12">

                                        <div class="form-group">
                                            <label class="text-sm" for="province">Pilih Provinsi : <span
                                                    class="required-ast">
                                                </span></label>
                                            <select required name="province_id" id="province_id"
                                                class="form-control form-control-sm" style="width:200px; width:100%;">
                                                <option value="">--- Pilih Provinsi ---</option>
                                                @foreach ($provinces as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">

                                            <label class="text-sm" for="regency">Pilih Kabupaten : <span
                                                    class="required-ast"></span></label>
                                            <select required name="regency_id" id="regency_id"
                                                class=" form-control form-control-sm" style="width:200px; width:100%;">
                                                <option value="">--Kabupaten--</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="text-sm" for="district">Pilih Kecamatan : </label>
                                            <select name="district_id" id="district"
                                                class="form-control form-control-sm" style="width:200px; width:100%;">
                                                <option value="">--Kecamatan--</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="text-sm" for="village">Pilih Desa:</label>
                                            <select name="village_id" id="village_id"
                                                class="form-control form-control-sm" style="width:200px; width:100%;">
                                                <option value="">--Desa--</option>

                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="text-sm">Alamat <span class="required-ast"></span></label>
                                            <textarea type="address" class="form-control form-control-sm"
                                                placeholder="Alamat E.g Jalan | Komplek | RT/RW" id="address_street"
                                                value="{{ old('address_street') }}" name="address_street"></textarea>
                                            @if ($errors->has('address_street'))
                                            <p class="text-sm text-red" class="error" for="first_name">
                                                {{ $errors->first('address_street') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div id="nav_owndata" class="row">
                                    <div class="col-6">
                                        <a href="#" class="text-start" onclick="account_process()"><i
                                                class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a href="#" class="mr-2" onclick="owndata_process()">Selanjutnya
                                            <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div id="profession_container" class="callout callout-info px-4 py-5 d-none">
                                <h4><i class="fas fa-briefcase mr-2"></i>Profesi</h4>
                                <hr>
                                <div>
                                    <div class="form-group">
                                        <label class="text-sm" for="status">NIP / No Reg / No Identitas Profesi
                                            (Opsional)
                                            : </label>
                                        <input type="text" class="form-control form-control-sm"
                                            placeholder="No Identitas Profesi" name="reg_number" id="reg_number">
                                    </div>

                                    <div class="form-group">
                                        <label class="text-sm" class="text-sm" for="type">Pilih Jenis User : <span
                                                class="required-ast text-red">
                                            </span></label>
                                        <select required name="type" id="type" class=" form-control form-control-sm"
                                            style="width:200px; width:100%;">
                                            @foreach ($type_user as $id => $val)
                                            <option value="{{ $id }}">{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="text-sm" for="status">Jenis Pekerjaan / Pilih Jabatan / Status
                                            : <span class="required-ast"></span></label>
                                        <select name="status" id="status" class=" form-control form-control-sm">
                                            <option selected="selected">--Jenis Jabatan / Status--</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="job_free_container" style="display:none">
                                        <label class="text-sm" for="status">Opsi Status : <span
                                                class="required-ast"></span></label>
                                        <input type="text" class="form-control form-control-sm"
                                            placeholder="Opsi Jenis Jabatan / Status" name="job_free" id="job_free">
                                    </div>
                                </div>

                                <div id="nav_profession" class="row">
                                    <div class="col-6">
                                        <a href="#" class="text-start" onclick="owndata_process()"><i
                                                class="fas fa-arrow-left"></i> Kembali</a>
                                    </div>
                                    <div class="col-6 text-right">
                                        <button type="submit"
                                            class="btn btn-outline-info btn-block login-page-button">Daftar</button>
                                    </div>
                                </div>
                            </div>



                        </form>
                    </div>

                </div>
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

    loadProfession();

    jQuery('select[name="province_id"]').on('change', function() {
        provinceValidation();
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
        regencyValidation();
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
        districtValidation();
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

    $('select[name="village_id"]').on('change', function() {
        villageValidation();
    });

    jQuery('select[name="type"]').on('change', function() {
        loadProfession();
    });

    $('select[name="status"]').on('change', function() {
        if ($('select[name="status"] option:selected').html() == 'Lainnya') {
            $("#job_free_container").css('display', 'block');
        } else {
            $("#job_free_container").css('display', 'none');
        }
    });
});

function loadProfession() {
    jQuery.ajax({
        url: '{{url("profesi")}}/' + jQuery('select[name="type"]').val(),
        type: "GET",
        dataType: "json",
        success: function(data) {
            jQuery('select[name="status"]').empty();
            jQuery.each(data, function(key, value) {
                $('select[name="status"]').append('<option value="' + key +
                    '">' + value + '</option>');
            });
        }
    });
}

$('input[name="username"]').on('input', function() {
    usernameValidation();
});

function usernameValidation() {
    validation($('input[name="username"]'), $('#username-feedback'), null, null);

    if ($('input[name="username"]').val().length > 5) {
        jQuery.ajax({
            url: '{{ route("usernamevalidation") }}',
            type: "post",
            data: {
                _token: "{{ csrf_token() }}",
                username: $('input[name="username"]').val()
            },
            success: function(data) {
                if (data == 1) {
                    validation($('input[name="username"]'), $('#username-feedback'),
                        'Diterima', true);
                } else {
                    validation($('input[name="username"]'), $('#username-feedback'),
                        'Sudah Digunakan', false);
                }
            }
        });
    } else {
        validation($('input[name="username"]'), $('#username-feedback'), 'Harus Lebih dari 5 Karakter', false);
    }
}

$('input[name="email"]').on('input', function() {
    emailValidation();
});

function emailValidation() {
    validation($('input[name="email"]'), $('#email-feedback'), null, null);

    if ($('input[name="email"]').val().indexOf("@") >= 0 && $('input[name="email"]').val().length > 5) {
        jQuery.ajax({
            url: '{{ route("emailvalidation") }}',
            type: "post",
            data: {
                _token: "{{ csrf_token() }}",
                email: $('input[name="email"]').val()
            },
            success: function(data) {
                if (data == 1) {
                    validation($('input[name="email"]'), $('#email-feedback'),
                        'Diterima', true);
                } else {
                    validation($('input[name="email"]'), $('#email-feedback'),
                        'Sudah Digunakan', false);
                }
            }
        });
    } else {
        validation($('input[name="email"]'), $('#email-feedback'), 'Email tidak Valid', false);
    }
}

$('input[name="password"]').on('input', function() {
    passwordValidation();
});

function passwordValidation() {
    validation($('input[name="password"]'), $('#password-feedback'), null, null);
    if ($('input[name="password"]').val().length > 6) {
        validation($('input[name="password"]'), $('#password-feedback'), 'Diterima', true);
    } else {
        validation($('input[name="password"]'), $('#password-feedback'), 'Harus Lebih dari 6 Karakter', false);
    }
}

$('input[name="c_password"]').on('input', function() {
    c_passwordValidation();
});

function c_passwordValidation() {
    validation($('input[name="c_password"]'), $('#c_password-feedback'), null, null);
    if ($('input[name="c_password"]').val().length > 6) {
        if ($('input[name="c_password"]').val() == $('input[name="password"]').val()) {
            validation($('input[name="c_password"]'), $('#c_password-feedback'), 'Diterima', true);
        } else {
            validation($('input[name="c_password"]'), $('#c_password-feedback'), 'Kata Sandi Tidak Sama',
                false);
        }

    } else {
        validation($('input[name="c_password"]'), $('#c_password-feedback'), 'Harus Lebih dari 6 Karakter',
            false);
    }
}

$('input[name="phone"]').on('input', function() {
    phoneValidation();
});

function phoneValidation() {

    validation($('input[name="phone"]'), $('#phone-feedback'), null, null);

    var a = $('input[name="phone"]').val();
    var filter =
        /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
    if (filter.test(a)) {
        validation($('input[name="phone"]'), $('#phone-feedback'), 'Diterima', true);
    } else {
        validation($('input[name="phone"]'), $('#phone-feedback'), 'Nomor tidak valid',
            false);
    }
}

function account_process() {
    usernameValidation();
    passwordValidation();
    c_passwordValidation();
    emailValidation();
    phoneValidation();

    if ($('#account_container').hasClass('d-none')) {
        $('#owndata_container').fadeOut();
        $('#owndata_container').addClass('d-none');
        $('#account_container').removeClass('d-none');
        $('#account_container').fadeIn();

    } else {
        if (!$('#account_container input').hasClass("is-invalid")) {
            $('#account_container').fadeOut();
            $('#account_container').addClass('d-none');
            $('#owndata_container').removeClass('d-none');
            $('#owndata_container').fadeIn();

        }

    }
}

function owndata_process() {
    firstNameValidation();
    lastNameValidation();
    birthPlaceValidation();
    birthDateValidation();
    ageValidation();
    provinceValidation();
    regencyValidation();
    districtValidation();
    villageValidation();
    addressValidation();

    if ($('#owndata_container').hasClass('d-none')) {
        $('#profession_container').fadeOut();
        $('#profession_container').addClass('d-none');
        $('#owndata_container').removeClass('d-none');
        $('#owndata_container').fadeIn();

    } else {
        if (!$('#owndata_container input').hasClass("is-invalid") && !$('#owndata_container select').hasClass(
                "is-invalid")) {
            $('#owndata_container').fadeOut();
            $('#owndata_container').addClass('d-none');
            $('#profession_container').removeClass('d-none');
            $('#profession_container').fadeIn();

        }
    }
}

$('input[name="first_name"]').on('input', function() {
    firstNameValidation();
});

function firstNameValidation() {
    validation($('input[name="first_name"]'), null, null, null);
    if ($('input[name="first_name"]').val().length > 0) {
        validation($('input[name="first_name"]'), null, 'Diterima', true);
    } else {
        validation($('input[name="first_name"]'), null, 'Harus Lebih dari 6 Karakter', false);
    }
}

$('input[name="last_name"]').on('input', function() {
    lastNameValidation();
});

function lastNameValidation() {
    validation($('input[name="last_name"]'), null, null, null);
    if ($('input[name="last_name"]').val().length > 0) {
        validation($('input[name="last_name"]'), null, 'Diterima', true);
    } else {
        validation($('input[name="last_name"]'), null, 'Harus Lebih dari 6 Karakter', false);
    }
}

$('input[name="birth_place"]').on('input', function() {
    birthPlaceValidation();
});

function birthPlaceValidation() {
    validation($('input[name="birth_place"]'), null, null, null);
    if ($('input[name="birth_place"]').val().length > 0) {
        validation($('input[name="birth_place"]'), null, null, true);
    } else {
        validation($('input[name="birth_place"]'), null, null, false);
    }
}

$('input[name="birth_date"]').on('input', function() {
    birthDateValidation();
});

function birthDateValidation() {
    validation($('input[name="birth_date"]'), null, null, null);
    if ($('input[name="birth_date"]').val().length > 0) {
        validation($('input[name="birth_date"]'), null, null, true);
    } else {
        validation($('input[name="birth_date"]'), null, null, false);
    }
}

$('input[name="age"]').on('input', function() {
    ageValidation();
});

function ageValidation() {
    validation($('input[name="age"]'), null, null, null);
    if ($('input[name="age"]').val().length > 0) {
        validation($('input[name="age"]'), null, null, true);
    } else {
        validation($('input[name="age"]'), null, null, false);
    }
}

$('textarea[name="address_street"]').on('input', function() {
    addressValidation();
});

function addressValidation() {
    validation($('textarea[name="address_street"]'), null, null, null);
    if ($('textarea[name="address_street"]').val().length > 0) {
        validation($('textarea[name="address_street"]'), null, null, true);
    } else {
        validation($('textarea[name="address_street"]'), null, null, false);
    }
}

function provinceValidation() {
    validation($('select[name="province_id"]'), null, null, null);
    if ($('select[name="province_id"]').val() != '') {
        validation($('select[name="province_id"]'), null, null, true);
    } else {
        validation($('select[name="province_id"]'), null, null, false);
    }
}

function regencyValidation() {
    validation($('select[name="regency_id"]'), null, null, null);
    if ($('select[name="regency_id"]').val() != '') {
        validation($('select[name="regency_id"]'), null, null, true);
    } else {
        validation($('select[name="regency_id"]'), null, null, false);
    }
}

function districtValidation() {
    validation($('select[name="district_id"]'), null, null, null);
    if ($('select[name="district_id"]').val().length != '') {
        validation($('select[name="district_id"]'), null, null, true);
    } else {
        validation($('select[name="district_id"]'), null, null, false);
    }
}

function villageValidation() {
    validation($('select[name="village_id"]'), null, null, null);
    if ($('select[name="village_id"]').val().length != '') {
        validation($('select[name="village_id"]'), null, null, true);
    } else {
        validation($('select[name="village_id"]'), null, null, false);
    }
}


function validation(element, feedback_id, msg, validity) {
    if (validity != null) {
        if (validity) {
            element.addClass('is-valid');
            if (feedback_id != null) {
                feedback_id.addClass('valid-feedback');
                feedback_id.html(msg);
            }

        } else {
            element.addClass('is-invalid');
            if (feedback_id != null) {
                feedback_id.addClass('invalid-feedback');
                feedback_id.html(msg);
            }
        }
    } else {
        element.removeClass('is-invalid');
        if (feedback_id != null) {
            feedback_id.removeClass('invalid-feedback');
            feedback_id.html(msg);
        }
    }
}
</script>

@endsection