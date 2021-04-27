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
            <section class="content">
                <h6 id="preloader" class=" text-center">
                    <i class="fas fa-spinner fa-spin"></i>
                </h6>

                <div id="container" class="container-fluid" hidden>
                    <!-- Small boxes (Stat box) -->
                    <div class="row">

                        <div class="col-md-12">
                            <div class="card card-light">
                                <div class="card-header">
                                    <h6 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Rekapitulasi</h6>

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

                                                @php
                                                $sumVal = 0;
                                                $districtsCount = 0;
                                                @endphp

                                                @foreach($percent as $item)
                                                <div class="progress-group text-left">
                                                    <small class="progress-text">{{ $item->name }}</small>
                                                    <small class="float-right">{{ $item->value }}%</small>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-{{ $item->value < 50 ? 'warning' : 'success' }}"
                                                            style="width: {{ $item->value }}%"></div>
                                                    </div>
                                                </div>
                                                @php
                                                $districtsCount += $item->districts_count;
                                                $sumVal += $item->passed;
                                                @endphp
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

                                                <p id="un"
                                                    class="description-percentage badge badge-secondary p-2 mb-1">
                                                    Pelaksanaan Mekop : {{ round(($sumVal / $districtsCount)*100 , 3) }}
                                                    %
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
<script>
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
    $.ajax({
        url: "{{ route('getkbpartratio.dashboard') }}",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
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

            $.each(data, function(i, data) {
                labels[i] = data.name;
                datasets[0].data[i] = data.un;
                datasets[1].data[i] = data.pa;
                datasets[2].data[i] = data.pus;

                pa = (pa + eval(data.pa));
                un = (un + eval(data.un));
                pus = (pus + eval(data.pus));

                $('#village_count').html(addCommas(data.village_count));
                $('#district_count').html(addCommas(data.districts_count));
                $('#regency_count').html(addCommas(data.regency_count));
                $('#province_name').html('PROVINSI ' + data.province_name);
            });

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
    $('#preloader').html('')
    $('#container').attr('hidden', false);
});
</script>

@endsection