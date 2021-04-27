@extends('layouts.frontend.frontend-template')

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

<body class="container-fluid paddding">
    <section class="content pt-4">

        <style>
        #mapid {
            height: 550px !important;
            width: 100% !important;
        }
        </style>

        <div class="card mx-1 my-2">
            <div id="mapid">

            </div>
        </div>
    </section>
</body>

<!--MODAL DETAIL-->
<div class="modal fade " tabindex="-1" role="dialog" id="detail_pusdiklat_modal">
    <div class="modal-dialog modal-lg card card-outline card-info" role="document">
        <div class="modal-content py-2 px-3">
            <div class="modal-header border-bottom-0">
                <div class="row">

                    <div class="col-lg-9 col-12">
                        <h5 id="name"></h5>
                    </div>
                    <div class="col-12">
                        <small id="address"></small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr class="mt-0">

            <div class="row">
                <div class="col-lg-5 col-12">
                    <img id="banner" class="card mb-3 w-100">
                </div>
                <div class="col-lg-7 col-12">
                    <div id="overview" class="px-3 mb-2">
                    </div>
                </div>
            </div>




            <hr class="mt-0">

            <nav>
                <div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link p_proper-screen_md active" id="nav-sdm-tab" data-toggle="tab"
                        href="#nav-sdm" role="tab" aria-controls="nav-sdm" aria-selected="true">Profil SDM</a>
                    <a class="nav-item nav-link p_proper-screen_md" id="nav-lmedia-tab" data-toggle="tab"
                        href="#nav-lmedia" role="tab" aria-controls="nav-lmedia" aria-selected="false">Media
                        Pelatihan</a>
                    <a class="nav-item nav-link p_proper-screen_md" id="nav-infr-tab" data-toggle="tab" href="#nav-infr"
                        role="tab" aria-controls="nav-infr" aria-selected="false">Saran & Prasarana</a>
                </div>
            </nav>
            <div class="tab-content mb-5 mt-2" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-sdm" role="tabpanel" aria-labelledby="nav-sdm-tab">
                </div>
                <div class="tab-pane fade" id="nav-lmedia" role="tabpanel" aria-labelledby="nav-lmedia-tab">

                </div>
                <div class="tab-pane fade" id="nav-infr" role="tabpanel" aria-labelledby="nav-infr-tab">...
                </div>
            </div>

        </div>
    </div>
</div>

@endsection


@section('javascript')
<script src="https://unpkg.com/esri-leaflet"></script>
<link rel="stylesheet" href="https://unpkg.com/esri-leaflet-geocoder/dist/esri-leaflet-geocoder.css" />
<script src="https://unpkg.com/esri-leaflet-geocoder"></script>

<script type="text/javascript">
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
                $('select[name="province_id"]').append('<option value="' + key + '">' +
                    value + '</option>');
            });
        }
    });
});

var newMarker = null;
var BigMarker = null;
//================================================================
var bigmap = L.map("mapid").setView([-1.7798011, 118.4451785], 5);
var bigbasemaps = {
    'Default': L.tileLayer('https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png', {
        attribution: 'Monalisa Map By Leaflet &copy',
        maxZoom: 20,
    }),
};

L.control.layers(bigbasemaps).addTo(bigmap);
bigbasemaps.Default.addTo(bigmap);

var searchControl = L.esri.Geocoding.geosearch({
    placeholder: 'Cari Lokasi Daerah atau Alamat'
}).addTo(bigmap);

var results = L.layerGroup().addTo(bigmap);

var popup = L.popup();

searchControl.on('results', function(data) {
    if (BigMarker !== null) {
        bigmap.removeLayer(BigMarker);
    }
    for (var i = data.results.length - 1; i >= 0; i--) {
        BigMarker = L.marker(data.results[i].latlng);
        results.addLayer(BigMarker);
    }
});

//logo position: bottomright, topright, topleft, bottomleft
var logo = L.control({
    position: 'bottomleft'
});

logo.onAdd = function(bigmap) {
    var div = L.DomUtil.create('div', '');
    div.innerHTML =
        '<div class="row card px-3 py-2 ml-1 mt-2 mb-4 align-content-center" style="background: rgba(0, 0, 0, 0.3)">' +
        '<a href="#" onclick="hidenav()" class="text-right"><i class="fas fa-minus text-white"></i></a>' +
        '<div id="map_nav_container">' +
        '<p class="text-white p_proper-screen_xl">Profil Mekop</p>' +
        '<h5 class="text-white p_proper-screen_xxl"><b>Mekop Berdasarkan Wilayah</b></h5>' +
        '<a href="#" onclick="resetDistrictSelection();" class="w-100 text-white pr-2 mr-2 border-right">Semua Kec <i class="fa fa-eye ml-1"></i></a>' +
        '<a href="#" onclick="resetRegencySelection();" class="w-100 text-white pr-2 mr-2 border-right">Semua Kota <i class="fa fa-eye ml-1"></i></a>' +
        '<a href="#" onclick="resetSelection();bigmapdatareload();" class="w-100 text-white">Lihat Semua <i class="fa fa-arrow-right"></i></a>' +
        '<select id="province_id" name="province_id" class="form-control form-control-sm p_proper-screen_xl mt-2"><option value="null" selected>--Semua Provinsi--</option></select>' +
        '<select id="regency_id" name="regency_id" class="form-control form-control-sm p_proper-screen_xl my-1"><option value="">--Pilih Provinsi--</option></select>' +
        '<select id="district_id" name="district_id" class="form-control form-control-sm p_proper-screen_xl my-1 mb-2"><option value="">--Pilih Kota / Kabupaten--</option></select>' +
        '</div>' +
        '</div>';
    return div;
}
logo.addTo(bigmap);

function hidenav() {
    if ($('#map_nav_container').hasClass('d-none')) {
        $('#map_nav_container').removeClass('d-none');
    } else {
        $('#map_nav_container').addClass('d-none');
    }
}

var preloader = L.control({
    position: 'topleft'
});

preloader.onAdd = function(bigmap) {
    var div = L.DomUtil.create('div', '');
    div.innerHTML =
        '<div class="card mt-3" style="background: rgba(0, 0, 0, 0.3)">' +
        '<h6 class="p-2"><i class="text-white fas fa-spinner fa-spin"></i> <small class="text-white p_proper-screen_sm ml-1">Memuat..</small></h6>' +
        '</div>';
    return div;
}

bigmap.on('zoomend', function(e) {
    var distanceX = countDistanceScreen();
    if (distanceX > 610) {
        preloader.addTo(bigmap);
        bigmapdatareload();
    } else if (distanceX < 610 && distanceX > 35) {
        preloader.addTo(bigmap);
        bigmapdataregencies(centerLatLng.lat, centerLatLng.lng, distanceX * 2)
    } else if (distanceX <= 35 && distanceX > 0) {
        preloader.addTo(bigmap);
        bigmapdatadistricts(centerLatLng.lat, centerLatLng.lng, distanceX * 2)
    }
});

bigmap.on('moveend', function(e) {
    var distanceX = countDistanceScreen();
    if (bigmapMarker.length > 0) {
        if (!bigmapMarker[bMIndexOnPopup].isPopupOpen()) {
            if (distanceX < 610 && distanceX > 35) {
                preloader.addTo(bigmap);
                bigmapdataregencies(centerLatLng.lat, centerLatLng.lng, distanceX * 2)
            } else if (distanceX <= 35 && distanceX > 0) {
                preloader.addTo(bigmap);
                bigmapdatadistricts(centerLatLng.lat, centerLatLng.lng, distanceX * 2)
            }
        }
    }
});

function countDistanceScreen() {
    centerLatLng = bigmap.getCenter();
    var pointC = bigmap.latLngToContainerPoint(centerLatLng);
    var pointX = [pointC.x + 1, pointC.y];
    var pointY = [pointC.x, pointC.y + 1];

    // convert containerpoints to latlng's
    var latLngC = bigmap.containerPointToLatLng(pointC);
    var latLngX = bigmap.containerPointToLatLng(pointX);
    var latLngY = bigmap.containerPointToLatLng(pointY);

    var distanceX = latLngC.distanceTo(latLngX);
    var distanceY = latLngC.distanceTo(latLngY);

    return distanceX;
}

var bigmapMarker = [];
var bMIndexOnPopup = 0;
var isBySelection;
preloader.addTo(bigmap);
bigmapdatareload();

$('select[name="province_id"]').on('change', function() {
    isBySelection = true;
    bigmapdatareload();

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
                    '<option value="null" selected>--Semua Kota Kabupaten--</option>');
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

$('select[name="regency_id"]').on('change', function() {
    isBySelection = true;
    bigmap.setView(centerLatLng, 10);

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
            }
        });
    } else {
        $('select[name="district_id"]').empty();
    }
});

$('select[name="district_id"]').on('change', function() {
    isBySelection = true;
    bigmap.setView(centerLatLng, 13);
});

function resetSelection() {
    $('select[name="province_id"]').val('null');
    $('select[name="regency_id"]').empty();
    $('select[name="regency_id"]').append('<option value="null" selected>--Pilih Provinsi--</option>');
    $('select[name="district_id"]').empty();
    $('select[name="district_id"]').append('<option value="null" selected>--Pilih Kota / Kabupaten--</option>');
    bigmapdatareload();
    bigmap.setView([-1.7798011, 118.4451785], 5);
}

function resetRegencySelection() {
    if ($('select[name="regency_id"] option').length > 1) {
        $('select[name="regency_id"]').val('null');
        $('select[name="district_id"]').empty();
        $('select[name="district_id"]').append('<option value="null" selected>--Pilih Kota / Kabupaten--</option>');
        bigmap.setView(centerLatLng, 9);
    }
}

function resetDistrictSelection() {
    if ($('select[name="district_id"] option').length > 1) {
        $('select[name="district_id"]').val('null');
        bigmap.setView(centerLatLng, 13);
    }
}

function bigmapdatareload() {
    $.ajax({
        url: "{{ route('fullprovinces.get') }}",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        data: {
            province_id: $('select[name="province_id"]').val(),
        },

        success: function(data) {
            if (bigmapMarker.length > 0) {
                for (i = 0; i < bigmapMarker.length; i++) {
                    bigmap.removeLayer(bigmapMarker[i]);
                }
            }

            $.each(data, function(i) {
                var status;
                var color;

                if (data[i].percentage >= 50) {
                    status = 'Tidak Aktif';
                    color = 'danger';

                    var greenIcon = new L.divIcon({
                        className: 'custom-div-icon',
                        html: '<img src="{{ asset("assets/leaflet/images/marker-icon.png") }}">',
                        iconUrl: '{{ asset("assets/leaflet/images/marker-icon.png") }}',
                        shadowUrl: '{{ asset("assets/leaflet/images/marker-shadow.png") }}',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });
                } else {
                    status = 'Aktif';
                    color = 'success';
                    var greenIcon = new L.divIcon({
                        className: 'custom-div-icon',
                        html: '<img src="{{ asset("assets/leaflet/images/marker-icon-red.png") }}">',
                        iconUrl: '{{ asset("assets/leaflet/images/marker-icon-red.png") }}',
                        shadowUrl: '{{ asset("assets/leaflet/images/marker-shadow.png") }}',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });
                }

                ltlng = [data[i].latitude, data[i].longitude];

                bigmapMarker[i] = new L.marker(ltlng, {
                    icon: greenIcon
                }).addTo(bigmap);

                bigmapMarker[i].on('click', function(event) {
                    onClickMarker(this, event, data[i].id);
                    bMIndexOnPopup = i;
                });

                if ($('select[name="province_id"]').val() != null && $('select[name="province_id"]')
                    .val() != "null" && isBySelection) {
                    bigmap.setView([data[i].latitude, data[i].longitude], 7);
                    isBySelection = false;
                }
            });

            preloader.remove();
        },
        error: function(jqXHR, textStatus, errorThrown) {

        },
    });


    function onClickMarker(ctx, event, province_id) {
        ctx = ctx;
        if (ctx.getPopup() == null) {
            popupContent = '<i class="fas fa-spinner fa-spin"></i>';

            ctx.bindPopup(popupContent);
            ctx.openPopup();

            $.ajax({
                url: "{{ route('getpopupdata') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    province_id: province_id,
                },
                dataType: "json",
                async: false,
                success: function(data) {
                    var popupContent =
                        '<div id="' + data.id +
                        '" class="mappopup bg-white rounded-lg p-2" style="min-width:180px !important;max-width:240px !important;">' +
                        '<h1 class="h6 font-weight-bold text-center mx-3">' + data.province + '</h1>' +

                        '<div class="col-12 text-center  mb-4">' +
                        '<div class="h4 font-weight-bold">' + data.disctricts_count +
                        '</div><p  style="line-height: 1.2; margin-top:-2px;" class="small text-gray">Kecamatan</p>' +
                        '</div>' +


                        '<div class="progress-c mx-auto" data-value="' + data.percentage + '">' +
                        '<span class="progress-c-left"><span class="progress-c-bar"></span>' +
                        '</span>' +
                        '<span class="progress-c-right"><span class="progress-c-bar"></span>' +
                        '</span>' +
                        '<div class="progress-c-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">' +
                        '<div class="h2 font-weight-bold">' + data.percentage +
                        '<sup class="small">%</sup></div></div>' +
                        '</div>' +

                        '<div class="row text-center mt-4">' +
                        '<div class="col-6 border-right">' +
                        '<div class="h4 font-weight-bold">' + data.passed +
                        '</div><p style="line-height: 1.2; margin-top:-2px;" class="small text-gray">Kecamatan Melaksanakan</p>' +
                        '</div>' +
                        '<div class="col-6">' +
                        '<div class="h4 font-weight-bold">' + data.ongoing +
                        '</div><p  style="line-height: 1.2; margin-top:-2px;" class="small text-gray">Kecamatan Dalam Progress</p>' +
                        '</div>' +
                        '</div>' +


                        '<div class="col-sm-12 text-center mt-3"><button class="btn btn-sm btn-outline-info my-2 my-sm-0 " onclick="detailProvinsiView(' +
                        +(20) + +')"> Selengkapnya</button></div>' +
                        '</div>' +
                        '</div>';

                    ctx.bindPopup(openPopup($(popupContent), data.percentage));
                }
            });
        }
    }
}

function bigmapdataregencies(latitude, longitude, radius) {
    $.ajax({
        url: "{{ route('fullregency.get') }}",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        data: {
            province_id: $('select[name="province_id"]').val(),
            regency_id: $('select[name="regency_id"]').val(),
            latitude: latitude,
            longitude: longitude,
            radius: radius,
        },

        success: function(data) {
            if (bigmapMarker.length > 0) {
                for (i = 0; i < bigmapMarker.length; i++) {
                    bigmap.removeLayer(bigmapMarker[i]);
                }
            }

            $.each(data, function(i) {
                var status;
                var color;

                if (data[i].percentage >= 50) {
                    status = 'Tidak Aktif';
                    color = 'danger';

                    var greenIcon = new L.divIcon({
                        className: 'custom-div-icon',
                        html: '<img src="{{ asset("assets/leaflet/images/marker-icon.png") }}">',
                        iconUrl: '{{ asset("assets/leaflet/images/marker-icon.png") }}',
                        shadowUrl: '{{ asset("assets/leaflet/images/marker-shadow.png") }}',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });
                } else {
                    status = 'Aktif';
                    color = 'success';
                    var greenIcon = new L.divIcon({
                        className: 'custom-div-icon',
                        html: '<img src="{{ asset("assets/leaflet/images/marker-icon-red.png") }}">',
                        iconUrl: '{{ asset("assets/leaflet/images/marker-icon-red.png") }}',
                        shadowUrl: '{{ asset("assets/leaflet/images/marker-shadow.png") }}',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });
                }

                ltlng = [data[i].latitude, data[i].longitude];

                bigmapMarker[i] = new L.marker(ltlng, {
                    icon: greenIcon
                }).addTo(bigmap);

                bigmapMarker[i].on('click', function(event) {
                    onClickMarker(this, event, data[i].province_id, data[i].id);
                    bMIndexOnPopup = i;
                });

                if ($('select[name="regency_id"]').val() != null && $('select[name="regency_id"]')
                    .val() != "null" && isBySelection) {
                    bigmap.setView([data[i].latitude, data[i].longitude], 10);
                    isBySelection = false;
                }
            });


            preloader.remove();
        },
        error: function(jqXHR, textStatus, errorThrown) {

        },
    });

    function onClickMarker(ctx, event, province_id, regency_id) {
        ctx = ctx;

        if (ctx.getPopup() == null) {
            popupContent = '<i class="fas fa-spinner fa-spin"></i>';

            ctx.bindPopup(popupContent);
            ctx.openPopup();

            $.ajax({
                url: "{{ route('getregencypopupdata') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    province_id: province_id,
                    regency_id: regency_id,
                },
                dataType: "json",
                async: false,
                success: function(data) {
                    var popupContent =
                        '<div id="' + data.id +
                        '" class="mappopup bg-white rounded-lg p-2" style="min-width:180px !important;max-width:240px !important;">' +
                        '<h1 class="h6 font-weight-bold text-center mx-3">' + data.regency + '</h1>' +

                        '<div class="col-12 text-center  mb-4">' +
                        '<div class="h4 font-weight-bold">' + data.disctricts_count +
                        '</div><p  style="line-height: 1.2; margin-top:-2px;" class="small text-gray">Kecamatan</p>' +
                        '</div>' +


                        '<div class="progress-c mx-auto" data-value="' + data.percentage + '">' +
                        '<span class="progress-c-left"><span class="progress-c-bar"></span>' +
                        '</span>' +
                        '<span class="progress-c-right"><span class="progress-c-bar"></span>' +
                        '</span>' +
                        '<div class="progress-c-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">' +
                        '<div class="h2 font-weight-bold">' + data.percentage +
                        '<sup class="small">%</sup></div></div>' +
                        '</div>' +

                        '<div class="row text-center mt-4">' +
                        '<div class="col-6 border-right">' +
                        '<div class="h4 font-weight-bold">' + data.passed +
                        '</div><p style="line-height: 1.2; margin-top:-2px;" class="small text-gray">Kecamatan Melaksanakan</p>' +
                        '</div>' +
                        '<div class="col-6">' +
                        '<div class="h4 font-weight-bold">' + data.ongoing +
                        '</div><p  style="line-height: 1.2; margin-top:-2px;" class="small text-gray">Kecamatan Dalam Progress</p>' +
                        '</div>' +
                        '</div>' +


                        '<div class="col-sm-12 text-center mt-3"></div>' +
                        '</div>' +
                        '</div>';

                    ctx.bindPopup(openPopup($(popupContent), data.percentage));
                }
            });

        }
    }
}

function bigmapdatadistricts(latitude, longitude, radius) {
    $.ajax({
        url: "{{ route('fulldistrict.get') }}",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        data: {
            province_id: $('select[name="province_id"]').val(),
            regency_id: $('select[name="regency_id"]').val(),
            district_id: $('select[name="district_id"]').val(),
            latitude: latitude,
            longitude: longitude,
            radius: radius,
        },

        success: function(data) {
            if (bigmapMarker.length > 0) {
                for (i = 0; i < bigmapMarker.length; i++) {
                    bigmap.removeLayer(bigmapMarker[i]);
                }
            }

            $.each(data, function(i) {
                var status;
                var color;

                if (data[i].percentage >= 50) {
                    status = 'Tidak Aktif';
                    color = 'danger';

                    var greenIcon = new L.divIcon({
                        className: 'custom-div-icon',
                        html: '<img src="{{ asset("assets/leaflet/images/marker-icon.png") }}">',
                        iconUrl: '{{ asset("assets/leaflet/images/marker-icon.png") }}',
                        shadowUrl: '{{ asset("assets/leaflet/images/marker-shadow.png") }}',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });
                } else {
                    status = 'Aktif';
                    color = 'success';
                    var greenIcon = new L.divIcon({
                        className: 'custom-div-icon',
                        html: '<img src="{{ asset("assets/leaflet/images/marker-icon-red.png") }}">',
                        iconUrl: '{{ asset("assets/leaflet/images/marker-icon-red.png") }}',
                        shadowUrl: '{{ asset("assets/leaflet/images/marker-shadow.png") }}',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });
                }

                ltlng = [data[i].latitude, data[i].longitude];

                bigmapMarker[i] = new L.marker(ltlng, {
                    icon: greenIcon
                }).addTo(bigmap);

                bigmapMarker[i].on('click', function(event) {
                    onClickMarker(this, event, data[i].province_id, data[i].regency_id,
                        data[i].id);
                    bMIndexOnPopup = i;
                });

                if ($('select[name="district_id"]').val() != null && $('select[name="district_id"]')
                    .val() != "null" && isBySelection) {
                    bigmap.setView([data[i].latitude, data[i].longitude], 14);
                    isBySelection = false;
                }
            });

            preloader.remove();
        },
        error: function(jqXHR, textStatus, errorThrown) {

        },
    });

    function onClickMarker(ctx, event, province_id, regency_id, district_id) {
        ctx = ctx;

        if (ctx.getPopup() == null) {
            popupContent = '<i class="fas fa-spinner fa-spin"></i>';

            ctx.bindPopup(popupContent);
            ctx.openPopup();

            $.ajax({
                url: "{{ route('getdistrictpopupdata') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    province_id: province_id,
                    regency_id: regency_id,
                    district_id: district_id,
                },
                dataType: "json",
                success: function(data) {
                    var popupContent =
                        '<div id="' + data.id +
                        '" class="mappopup bg-white rounded-lg p-2" style="min-width:180px !important;max-width:240px !important;">' +
                        '<h1 class="h6 font-weight-bold text-center mx-3">' + data.district + '</h1>' +

                        '<div class="col-12 text-center  mb-4">' +
                        '<div class="h4 font-weight-bold">' + data.villages_count +
                        '</div><p  style="line-height: 1.2; margin-top:-2px;" class="small text-gray">Kelurahan / Desa</p>' +
                        '</div>' +


                        '<div class="progress-c mx-auto" data-value="' + data.percentage + '">' +
                        '<span class="progress-c-left"><span class="progress-c-bar"></span>' +
                        '</span>' +
                        '<span class="progress-c-right"><span class="progress-c-bar"></span>' +
                        '</span>' +
                        '<div class="progress-c-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">' +
                        '<div class="h2 font-weight-bold">' + (data.passed > 0 ?
                            '<h2><i class="fas fa-check text-success"></i><h2>' :
                            '<h2><i class="fas fa-times text-danger"></i><h2>') +
                        '<sup class="small"></sup></div></div>' +
                        '</div>' +

                        '<div class="row text-center mt-4">' +
                        '<div class="col-12 text-center ">' +

                        '<p class="small text-gray text-center m-0">' +
                        (
                            data.passed > 0 ? 'Telah Memenuhi Indikator Mekop' :
                            'Belum Memenuhi Indikator Mekop'
                        ) + '</p>' +
                        '</div>' +
                        '</div>' +


                        '<div class="col-sm-12 text-center mt-3"><button class="btn btn-sm btn-outline-info my-2 my-sm-0 " onclick="detailProvinsiView(' +
                        +(20) + +')"> Selengkapnya</button></div>' +
                        '</div>' +
                        '</div>';

                    ctx.bindPopup(openPopup($(popupContent), data.percentage));
                }
            });
        }
    }
}

function openPopup(rootView, value) {
    rootView.find(".progress-c").each(function() {

        var left = $(this).find('.progress-c-left .progress-c-bar');
        var right = $(this).find('.progress-c-right .progress-c-bar');

        if (value >= 0 && value < 50) {
            right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')

            $(this).find(".progress-c-bar").addClass("border-danger");
        } else if (value >= 50 && value < 70) {
            right.css('transform', 'rotate(180deg)')
            left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')

            $(this).find(".progress-c-bar").addClass("border-info");

        } else if (value >= 70 && value <= 100) {
            right.css('transform', 'rotate(180deg)')
            left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')

            $(this).find(".progress-c-bar").addClass("border-success");
        }
    });

    function percentageToDegrees(percentage) {
        return percentage / 100 * 360
    }

    return rootView[0].outerHTML;
}
</script>
@endsection