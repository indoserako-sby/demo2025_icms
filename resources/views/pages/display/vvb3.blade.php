@extends('layouts.display')
@push('title')
    Industrial Assets Monitoring
@endpush

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/gauge.js') }}"></script>
    <style>
        #chart1,
        #chart2,
        #chart3,
        #chart4,
        #chart5,
        #chart6,
        #chart7,
        #chart8,
        #chart9,
        #chart10,
        #chart11,
        #chart12,
        #chart13 {
            height: 280px;
        }

        @media screen and (max-width: 991px) {

            #chart1,
            #chart2,
            #chart3,
            #chart4,
            #chart5,
            #chart6,
            #chart7,
            #chart8,
            #chart9,
            #chart10,
            #chart11,
            #chart12,
            #chart13 {
                height: 200px;
            }
        }
    </style>
@endpush



@section('content')
    <div class="row" style="margin:10px;">
        <div class="col-md-1 flex-grow-0" style="max-width: 60px;">
            <div class="card card-body mb-2 d-flex justify-content-center align-items-center" style="height: 100%;">
                <span style="writing-mode: vertical-rl; transform: rotate(180deg); white-space: nowrap;">
                    Sensor 1
                </span>
            </div>
        </div>
        <div class="col-md-11 flex-grow-1" style="max-width: calc(100% - 60px);">
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="card card-body mb-2">
                        Vrms
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card shadow">
                                <div class="card-header">
                                    <h4 class="card-title">Peb</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3 text-center">
                                            <canvas id="gauge1" width="120" height="100"></canvas>
                                            <div style="margin-top: 0px"></div>
                                            Xrms-x222
                                            <h4 class="text-bold m-0"><span id="txt_gauge_1">0</span></h4>
                                            <div class="text-sm">adadad</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-body">
                        ARMS
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-body">
                        Apeak
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-body">
                        Temperature
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-body">
                        Bearing
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-body">
                        Unbalanced
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Gauge.js  -->
    <script>
        var opts = {
            angle: -0.2, // The span of the gauge arc
            lineWidth: 0.26, // The line thickness
            radiusScale: 1, // Relative radius
            pointer: {
                length: 0.54, // // Relative to gauge radius
                strokeWidth: 0.033, // The thickness
                color: '#000000' // Fill color
            },
            limitMax: true, // If false, max value increases automatically if value > maxValue
            limitMin: false, // If true, the min value of the gauge will be fixed
            colorStart: '#007bff', // Colors
            colorStop: '#007bff', // just experiment with them
            strokeColor: '#E0E0E0', // to see which ones work best for you
            generateGradient: true,
            highDpiSupport: true, // High resolution support

        };
        var target1 = document.getElementById('gauge1'); // your canvas element
        var gauge1 = new Gauge(target1).setOptions(opts); // create sexy gauge!
        gauge1.setMinValue(0); // Prefer setter over gauge.minValue = 0
        gauge1.animationSpeed = 32; // set animation speed (32 is default value)
        gauge1.set(0); // set actual value
    </script>
@endpush
