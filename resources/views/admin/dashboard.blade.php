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

        <div class="content-wrapper pt-3">
            <section class="content">
                <h6 id="preloader" class=" text-center">
                    <i class="fas fa-spinner fa-spin"></i>
                </h6>

                <div id="container" class="container-fluid" hidden>
                    <!-- Small boxes (Stat box) -->
                    <div class="row">

                        <div class="col-md-12">
                            <div class="card card-light">
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
                            </div>
                            <!-- /.card -->
                        </div>

                        <div class="col-md-12">
                            <div class="card card-light">
                                <div class="card-header">
                                    <h6 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Rekapitulasi Nasional
                                    </h6>

                                    <div class="card-tools">
                                        <a id="btn-dwnld" href="#" class="" target="_blank">
                                            <i class="fas fa-download mr-1"></i>
                                        </a>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <p class="text-center my-0">
                                                <strong>GRAFIK PESERTA KB</strong>
                                            </p>

                                            <!-- /.d-flex -->
                                            <div class="chartWrapper text-center">
                                                <div class="XWrapper pb-3">
                                                    <p id="chartratiopreloader" class="badge badge-secondary p-2"><i
                                                            class="fas fa-spinner fa-spin mr-2"></i>Memuat...</p>
                                                    <canvas id="sales-chart"
                                                        style="height:350px !important;width:5500px !important;"></canvas>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- /.col -->
                                        <div class="col-md-4">
                                            <p class="text-center my-0">
                                                <strong>PERSENTASE PELAKSANAAN</strong>
                                            </p>

                                            <div class="YWrapper text-center mt-3">
                                                @foreach($percent as $item)
                                                <div class="progress-group text-left">
                                                    <small class="progress-text">{{ $item->name }}</small>
                                                    <small class="float-right">{{ $item->value }}%</small>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-{{ $item->value < 50 ? 'warning' : 'success' }}"
                                                            style="width: {{ $item->value }}%"></div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->
                                </div>
                                <!-- ./card-body -->
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-sm-3 col-6">
                                            <div class="description-block border-right">
                                                <h3 class="description-percentage text-warning">
                                                    <i class="fas fa-landmark text-info"></i>
                                                </h3>
                                                <h5 id="regency_count" class="description-header">0</h5>
                                                <small class="description-text">Kota Kabupaten</small>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-6">
                                            <div class="description-block border-right">
                                                <h3 class="description-percentage text-success">
                                                    <i class="fas fa-landmark text-success"></i>
                                                </h3>
                                                <h5 id="district_count" class="description-header">0</h5>
                                                <small class="description-text">Kecamatan</small>
                                            </div>
                                        </div>

                                        <div class="col-sm-3 col-6">
                                            <div class="description-block border-right">
                                                <h3 class="description-percentage text-warning">
                                                    <i class="fas fa-landmark text-warning"></i>
                                                </h3>
                                                <h5 id="village_count" class="description-header">0</h5>
                                                <small class="description-text">Desa Kelurahan</small>
                                            </div>
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-sm-3 col-6">
                                            <div class="description-block text-left">
                                                <p id="pus" class="description-percentage badge badge-success p-2 mb-1">
                                                    <i class="fas fa-users"></i>
                                                </p><br>
                                                <p id="pa" class="description-percentage badge badge-info p-2 mb-1"><i
                                                        class="fas fa-users"></i></p><br>
                                                <p id="un" class="description-percentage badge badge-danger p-2 mb-1"><i
                                                        class="fas fa-users"></i></p>

                                                <hr>
                                                <p class="description-percentage badge badge-secondary p-2 mb-1 w-100">
                                                    Mekop Nasional :
                                                    <span class="mekop_nasional_bottom"></span>
                                                </p>
                                            </div>
                                            <!-- /.description-block -->
                                        </div>
                                    </div>
                                    <!-- /.row -->
                                </div>
                                <!-- /.card-footer -->
                            </div>
                            <!-- /.card -->
                        </div>

                    </div>
                </div>
            </section>
        </div>
</body>
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
        '<p class="text-white p_proper-screen_xl my-0">Jumlah Nasional <span class="text-white p_proper-screen_xl my-0 pus_nasional"></span></p>' +
        '<h5 class="text-white p_proper-screen_xxl"><b>Pasangan Usia Subur</b></h5>' +

        '<a href="#" onclick="resetSelection();bigmapdatareload();" class="w-100 text-white">Lihat Semua <i class="fa fa-arrow-right"></i></a>' +
        '<select id="province_id" name="province_id" class="form-control form-control-sm p_proper-screen_xl mt-2 mb-3"><option value="null" selected>--Semua Provinsi--</option></select>' +
        '</div>' +
        '</div>';
    return div;
}
logo.addTo(bigmap);

//logo position: bottomright, topright, topleft, bottomleft
var infoGraph = L.control({
    position: 'bottomright'
});
infoGraph.onAdd = function(bigmap) {
    var div = L.DomUtil.create('div', '');
    div.innerHTML =
        '<div class="row card px-3 py-2 mr-1 mt-2 mb-4 align-content-center" style="background: rgba(0, 0, 0, 0.3)">' +
        '<div id="map_nav_container" class="text-right">' +
        '<p class="text-white p_proper-screen_xl my-0">Persentase</p> <span class="text-white p_proper-screen_xl my-0 mekop_nasional h6"></span>' +

        '</div>' +
        '</div>';
    return div;
}
infoGraph.addTo(bigmap);

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

function bigmapdatareload() {
    $.ajax({
        url: "{{ route('getkbpartratio.dashboard') }}",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        data: {
            province_id: $('select[name="province_id"]').val(),
        },
        success: function(data) {
            var labels = [];
            var datasets = [{
                    label: 'Peserta Tidak Terlayani',
                    borderColor: 'rgba(186, 23, 23, .5)',
                    backgroundColor: 'rgba(186, 23, 23, .1)',
                    data: []
                },
                {
                    label: 'Peserta KB Aktif',
                    borderColor: 'rgba(23, 162, 184, .5)',
                    backgroundColor: 'rgba(23, 162, 184, .1)',
                    data: []
                },
                {
                    label: 'Pasangan Usia Subur',
                    borderColor: 'rgba(40, 167, 69, .5)',
                    backgroundColor: 'rgba(40, 167, 69, .1)',
                    data: []
                }
            ];

            var pa = 0;
            var un = 0;
            var pus = 0;

            if (bigmapMarker.length > 0) {
                for (i = 0; i < bigmapMarker.length; i++) {
                    bigmap.removeLayer(bigmapMarker[i]);
                }
            }

            regencyCount = 0;
            districtCount = 0;
            villageCount = 0;

            $.each(data, function(i, data) {
                labels[i] = data.name;
                datasets[0].data[i] = data.un;
                datasets[1].data[i] = data.pa;
                datasets[2].data[i] = data.pus;

                pa = (pa + eval(data.pa));
                un = (un + eval(data.un));
                pus = (pus + eval(data.pus));

                regencyCount += data.regency_count;
                districtCount += data.districts_count;
                villageCount += data.village_count;
                $('#province_name').html('PROVINSI ' + data.province_name);

                $('.mekop_nasional').html('<b>' + data.worked_count + '%</b>');
                $('.mekop_nasional_bottom').html('<b>' + data.worked_count + '%</b>');
            });

            $('#regency_count').html(addCommas(regencyCount));
            $('#district_count').html(addCommas(districtCount));
            $('#village_count').html(addCommas(villageCount));

            $('.pus_nasional').html(addCommas(pus));


            $.each(data, function(i, data) {
                var status;
                var color;

                ltlng = [data.latitude, data.longitude];

                pusAvg = (pus / data.length);

                color = 'blue';
                if (data.pus > pusAvg) {
                    color = 'red';
                } else {
                    color = 'green';
                }

                bigmapMarker[i] = new L.circle(ltlng, (eval(data.pus) / 30), {
                    color: color,
                    opacity: .5
                }).addTo(bigmap);

                bigmapMarker[i].on('click', function(event) {
                    onClickMarker(this, event, data.province_id);
                    bMIndexOnPopup = i;
                });

                if ($('select[name="province_id"]').val() != null && $(
                        'select[name="province_id"]')
                    .val() != "null" && isBySelection) {
                    bigmap.setView([data.latitude, data.longitude], 7);
                    isBySelection = false;
                }
            });

            preloader.remove();

            $('#pa').html('<i class="fas fa-users mr-1"></i>PA ' + addCommas(pa));
            $('#un').html('<i class="fas fa-users mr-1"></i>UN ' + addCommas(un));
            $('#pus').html('<i class="fas fa-users mr-1"></i>PUS ' + addCommas(pus));


            chartratio.data.datasets = datasets;
            chartratio.data.labels = labels;
            chartratio.update();

            $('#btn-dwnld').unbind();
            $('#btn-dwnld').bind('click', function(e) {
                $('#btn-dwnld').attr('href', chartratio.toBase64Image());
                $('#btn-dwnld').attr('download', 'grafik_rasio_pkbplkb.png');
            });

            $('#chartratiopreloader').addClass('d-none');
        }
    });

    function onClickMarker(ctx, event, province_id) {
        ctx = ctx;
        if (ctx.getPopup() == null) {
            popupContent = '<i class="fas fa-spinner fa-spin"></i>';

            ctx.bindPopup(popupContent);
            ctx.openPopup();

            $.ajax({
                url: "{{ route('getdashboardpopupdata') }}",
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
                        '<h1 class="h6 font-weight-bold text-center mx-3">' + data.province +
                        '</h1>' +

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


                        '<div class="col-sm-12 text-center mt-3"><b>Jumlah PUS : ' + addCommas(data.pus) +
                        '</b>'
                    '</div>' +
                    '</div>' +
                    '</div>';

                    ctx.bindPopup(openPopup($(popupContent), data.percentage));
                }
            });
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

}
</script>

<script>
var pusnasional;
var chartratio = document.getElementById('sales-chart').getContext('2d');;
var chartratio = new Chart(chartratio, {
    type: 'line',
    data: {
        labels: [],
        datasets: []
    },
    options: {
        responsive: false,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    fontSize: 9
                }
            }],
            xAxes: [{
                ticks: {
                    fontSize: 9
                }
            }]
        },
    }
});

$(document).ready(function() {
    resetSelection();
});

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
</script>

<script>
$(document).ready(function() {
    setInterval(function() {
        bigmap.invalidateSize();
    }, 100);
});
</script>

@endsection