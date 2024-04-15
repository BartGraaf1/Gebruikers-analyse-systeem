@extends('layouts.user_type.auth')

@section('content')
    <div>
        @if(session('success'))
            <div class="alert alert-success mx-4" role="alert">
                <p class="mb-0">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mx-4" role="alert">
                <p class="mb-0">{{ session('error') }}</p>
            </div>
        @endif
        <div class="row mt-4">
            <div class="col-md-6 z-index-3">
                <div class="card">
                    <form action="{{ route('production.analyse', ['production' => $production]) }}" method="GET">
                        @csrf
                        <div class="card">
                            <input name="statistics_date" class="form-control datepicker" placeholder="Please select date" type="text" onfocus="focused(this)" onfocusout="defocused(this)">
                            <select name=statistics_fragments[]" multiple class="form-control js-choice" id="choices-button" placeholder="Departure">
                                @foreach ($allFragments as $fragment)
                                    <option value="{{ $fragment->id }}">{{ $fragment->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="submit" value="Submit">
                    </form>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card z-index-2">
                    <div class="card-header p-3 pb-0">
                        <h6>Loads + viewers</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="loads-&-viewers-chart" class="chart-canvas" height="300" style="display: block; box-sizing: border-box; height: 300px; width: 428.5px;" width="428"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-md-0 mt-4">
                <div class="card z-index-2">
                    <div class="card-header p-3 pb-0">
                        <h6>Line chart with gradient</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="line-chart-gradient" class="chart-canvas" height="300" style="display: block; box-sizing: border-box; height: 300px; width: 428.5px;" width="428"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card z-index-2">
                        <div class="card-header p-3 pb-0">
                            <h6>Bar chart</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="bar-chart" class="chart-canvas" height="300" style="display: block; box-sizing: border-box; height: 300px; width: 428.5px;" width="428"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-md-0 mt-4">
                    <div class="card z-index-2">
                        <div class="card-header p-3 pb-0">
                            <h6>Bar chart horizontal</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="bar-chart-horizontal" class="chart-canvas" height="300" style="display: block; box-sizing: border-box; height: 300px; width: 428.5px;" width="428"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card z-index-2">
                        <div class="card-header p-3 pb-0">
                            <h6>Mixed chart</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="viewing-range-chart" class="chart-canvas" height="300" style="display: block; box-sizing: border-box; height: 300px; width: 428.5px;" width="428"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-md-0 mt-4">
                    <div class="card z-index-2">
                        <div class="card-header p-3 pb-0">
                            <h6>Bubble chart</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="bubble-chart" class="chart-canvas" height="199" style="display: block; box-sizing: border-box; height: 199px; width: 428.5px;" width="428"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card z-index-2">
                        <div class="card-header p-3 pb-0">
                            <h6>Doughnut chart</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="doughnut-chart" class="chart-canvas" height="300" style="display: block; box-sizing: border-box; height: 300px; width: 428.5px;" width="428"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-md-0 mt-4">
                    <div class="card z-index-2">
                        <div class="card-header p-3 pb-0">
                            <h6>Pie chart</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="pie-chart" class="chart-canvas" height="300" style="display: block; box-sizing: border-box; height: 300px; width: 428.5px;" width="428"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card z-index-2">
                        <div class="card-header p-3 pb-0">
                            <h6>Radar chart</h6>
                        </div>
                        <div class="card-body p-5">
                            <div class="chart">
                                <canvas id="radar-chart" class="chart-canvas" height="364" style="display: block; box-sizing: border-box; height: 364px; width: 364.5px;" width="364"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-md-0 mt-4">
                    <div class="card z-index-2">
                        <div class="card-header p-3 pb-0">
                            <h6>Polar chart</h6>
                        </div>
                        <div class="card-body p-5">
                            <div class="chart">
                                <canvas id="polar-chart" class="chart-canvas" height="364" style="display: block; box-sizing: border-box; height: 364px; width: 364.5px;" width="364"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@endsection

@push('production-statistics')
<script>
    if (document.querySelector('.datepicker')) {
        flatpickr('.datepicker', {
            mode: "range"
        });
    }


    const element = document.querySelector('.js-choice');
    const choices = new Choices(element);


    // Line chart
    var ctx1 = document.getElementById("loads-&-viewers-chart").getContext("2d");
    var labels = @json($labels);
    var totalViews = @json($totalViews);
    var totalLoad = @json($totalLoad);

    new Chart(ctx1, {
        type: "line",
        data: {
            labels: labels, // Use dynamic labels from the server
            datasets: [{
                label: "Total Views",
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 2,
                pointBackgroundColor: "#cb0c9f",
                borderColor: "#cb0c9f",
                backgroundColor: "rgba(203, 12, 159, 0.1)", // Use a lighter color or gradient
                data: totalViews, // Use dynamic data from the server
                maxBarThickness: 6
            },
                {
                    label: "Total Load",
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 2,
                    pointBackgroundColor: "#3A416F",
                    borderColor: "#3A416F",
                    backgroundColor: "rgba(58, 65, 111, 0.1)", // Use a lighter color or gradient
                    data: totalLoad, // Use dynamic data from the server
                    maxBarThickness: 6
                }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true, // Set this to true to show the legend
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#b2b9bf',
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: true,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#b2b9bf',
                        padding: 10,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });

    // Line chart with gradient
    var ctx2 = document.getElementById("line-chart-gradient").getContext("2d");

    var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

    var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
    gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

    new Chart(ctx2, {
        type: "line",
        data: {
            labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Mobile apps",
                tension: 0.4,
                borderWidth: 0,
                pointRadius: 0,
                borderColor: "#cb0c9f",
                borderWidth: 3,
                backgroundColor: gradientStroke1,
                fill: true,
                data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                maxBarThickness: 6

            },
                {
                    label: "Websites",
                    tension: 0.4,
                    borderWidth: 0,
                    pointRadius: 0,
                    borderColor: "#3A416F",
                    borderWidth: 3,
                    backgroundColor: gradientStroke2,
                    fill: true,
                    data: [30, 90, 40, 140, 290, 290, 340, 230, 400],
                    maxBarThickness: 6
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#b2b9bf',
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#b2b9bf',
                        padding: 10,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });

    // Doughnut chart
    var ctx3 = document.getElementById("doughnut-chart").getContext("2d");

    new Chart(ctx3, {
        type: "doughnut",
        data: {
            labels: ['Creative Tim', 'Github', 'Bootsnipp', 'Dev.to', 'Codeinwp'],
            datasets: [{
                label: "Projects",
                weight: 9,
                cutout: 60,
                tension: 0.9,
                pointRadius: 2,
                borderWidth: 2,
                backgroundColor: ['#2152ff', '#3A416F', '#f53939', '#a8b8d8', '#cb0c9f'],
                data: [15, 20, 12, 60, 20],
                fill: false
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                    },
                    ticks: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                    },
                    ticks: {
                        display: false,
                    }
                },
            },
        },
    });

    // Pie chart
    var ctx4 = document.getElementById("pie-chart").getContext("2d");

    new Chart(ctx4, {
        type: "pie",
        data: {
            labels: ['Facebook', 'Direct', 'Organic', 'Referral'],
            datasets: [{
                label: "Projects",
                weight: 9,
                cutout: 0,
                tension: 0.9,
                pointRadius: 2,
                borderWidth: 2,
                backgroundColor: ['#17c1e8', '#cb0c9f', '#3A416F', '#a8b8d8'],
                data: [15, 20, 12, 60],
                fill: false
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                    },
                    ticks: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                    },
                    ticks: {
                        display: false,
                    }
                },
            },
        },
    });

    // Bar chart
    var ctx5 = document.getElementById("bar-chart").getContext("2d");

    new Chart(ctx5, {
        type: "bar",
        data: {
            labels: ['16-20', '21-25', '26-30', '31-36', '36-42', '42+'],
            datasets: [{
                label: "Sales by age",
                weight: 5,
                borderWidth: 0,
                borderRadius: 4,
                backgroundColor: '#3A416F',
                data: [15, 20, 12, 60, 20, 15],
                fill: false,
                maxBarThickness: 35
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#9ca2b7'
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: true,
                        drawTicks: true,
                    },
                    ticks: {
                        display: true,
                        color: '#9ca2b7',
                        padding: 10
                    }
                },
            },
        },
    });

    // Bar chart horizontal
    var ctx6 = document.getElementById("bar-chart-horizontal").getContext("2d");

    new Chart(ctx6, {
        type: "bar",
        data: {
            labels: ['16-20', '21-25', '26-30', '31-36', '36-42', '42+'],
            datasets: [{
                label: "Sales by age",
                weight: 5,
                borderWidth: 0,
                borderRadius: 4,
                backgroundColor: '#3A416F',
                data: [15, 20, 12, 60, 20, 15],
                fill: false
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#9ca2b7'
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: true,
                        drawTicks: true,
                    },
                    ticks: {
                        display: true,
                        color: '#9ca2b7',
                        padding: 10
                    }
                },
            },
        },
    });

    // Mixed chart
    var ctx7 = document.getElementById("viewing-range-chart").getContext("2d");

    new Chart(ctx7, {
        data: {
            labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                type: "bar",
                label: "Organic Search",
                weight: 5,
                tension: 0.4,
                borderWidth: 0,
                pointBackgroundColor: "#3A416F",
                borderColor: "#3A416F",
                backgroundColor: '#3A416F',
                borderRadius: 4,
                borderSkipped: false,
                data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                maxBarThickness: 10,
            },
                {
                    type: "line",
                    label: "Referral",
                    tension: 0.4,
                    borderWidth: 0,
                    pointRadius: 0,
                    pointBackgroundColor: "#cb0c9f",
                    borderColor: "#cb0c9f",
                    borderWidth: 3,
                    backgroundColor: gradientStroke1,
                    data: [30, 90, 40, 140, 290, 290, 340, 230, 400],
                    fill: true,
                }
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#b2b9bf',
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: true,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#b2b9bf',
                        padding: 10,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });

    {{--var ctx7 = document.getElementById("viewing-range-chart").getContext("2d");--}}
    {{--var labels = @json($productionDailyStats->pluck('day'));--}}
    {{--var viewersData = @json($productionDailyStats->pluck('total_views')); // Adjust according to your actual viewers data--}}
    {{--var differenceData10_20 = @json($percentagesDifferences->pluck('difference_10_20'));--}}

    {{--new Chart(ctx7, {--}}
    {{--    type: 'bar',--}}
    {{--    data: {--}}
    {{--        labels: labels,--}}
    {{--        datasets: [{--}}
    {{--            type: 'line',--}}
    {{--            label: 'Viewers',--}}
    {{--            data: viewersData,--}}
    {{--            borderColor: '#cb0c9f',--}}
    {{--            backgroundColor: 'rgba(203, 12, 159, 0.1)',--}}
    {{--            borderWidth: 3--}}
    {{--        }, {--}}
    {{--            type: 'bar',--}}
    {{--            label: 'Difference 10% to 20%',--}}
    {{--            data: differenceData10_20,--}}
    {{--            backgroundColor: '#3A416F',--}}
    {{--            maxBarThickness: 10--}}
    {{--        }]--}}
    {{--    },--}}
    {{--    options: {--}}
    {{--        responsive: true,--}}
    {{--        maintainAspectRatio: false,--}}
    {{--        plugins: {--}}
    {{--            legend: {--}}
    {{--                display: true--}}
    {{--            }--}}
    {{--        },--}}
    {{--        scales: {--}}
    {{--            y: {--}}
    {{--                grid: {--}}
    {{--                    drawBorder: false,--}}
    {{--                    display: true--}}
    {{--                },--}}
    {{--                ticks: {--}}
    {{--                    display: true--}}
    {{--                }--}}
    {{--            },--}}
    {{--            x: {--}}
    {{--                grid: {--}}
    {{--                    drawBorder: false,--}}
    {{--                    display: true--}}
    {{--                },--}}
    {{--                ticks: {--}}
    {{--                    display: true--}}
    {{--                }--}}
    {{--            }--}}
    {{--        }--}}
    {{--    }--}}
    {{--});--}}

    // Bubble chart
    var ctx8 = document.getElementById("bubble-chart").getContext("2d");

    new Chart(ctx8, {
        type: "bubble",
        data: {
            labels: ['0', '10', '20', '30', '40', '50', '60', '70', '80', '90'],
            datasets: [{
                label: 'Dataset 1',
                data: [{
                    x: 100,
                    y: 0,
                    r: 10
                }, {
                    x: 60,
                    y: 30,
                    r: 20
                }, {
                    x: 40,
                    y: 350,
                    r: 10
                }, {
                    x: 80,
                    y: 80,
                    r: 10
                }, {
                    x: 20,
                    y: 30,
                    r: 15
                }, {
                    x: 0,
                    y: 100,
                    r: 5
                }],
                backgroundColor: '#cb0c9f',
            },
                {
                    label: 'Dataset 2',
                    data: [{
                        x: 70,
                        y: 40,
                        r: 10
                    }, {
                        x: 30,
                        y: 60,
                        r: 20
                    }, {
                        x: 10,
                        y: 300,
                        r: 25
                    }, {
                        x: 60,
                        y: 200,
                        r: 10
                    }, {
                        x: 50,
                        y: 300,
                        r: 15
                    }, {
                        x: 20,
                        y: 350,
                        r: 5
                    }],
                    backgroundColor: '#3A416F',
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#b2b9bf',
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#b2b9bf',
                        padding: 10,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });

    // Radar chart
    var ctx9 = document.getElementById("radar-chart").getContext("2d");

    new Chart(ctx9, {
        type: "radar",
        data: {
            labels: ["English", "Maths", "Physics", "Chemistry", "Biology", "History"],
            datasets: [{
                label: "Student A",
                backgroundColor: "rgba(58,65,111,0.2)",
                data: [65, 75, 70, 80, 60, 80],
                borderDash: [5, 5],
            }, {
                label: "Student B",
                backgroundColor: "rgba(203,12,159,0.2)",
                data: [54, 65, 60, 70, 70, 75]
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    });

    // Radar chart
    var ctx10 = document.getElementById("polar-chart").getContext("2d");

    new Chart(ctx10, {
        type: "polarArea",
        data: {
            labels: [
                'Red',
                'Green',
                'Yellow',
                'Grey',
                'Blue'
            ],
            datasets: [{
                label: 'My First Dataset',
                data: [11, 16, 7, 3, 14],
                backgroundColor: ['#17c1e8', '#cb0c9f', '#3A416F', '#a8b8d8', '#82d616'],
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    });
</script>
@endpush
