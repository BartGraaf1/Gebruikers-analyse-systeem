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
                        <h6>Viewing range visualized</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="viewing-range-sum" class="chart-canvas" height="300" style="display: block; box-sizing: border-box; height: 300px; width: 428.5px;" width="428"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card z-index-2">
                        <div class="card-header p-3 pb-0">
                            <h6>Average viewing range</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="average-viewing-range" class="chart-canvas" height="300" style="display: block; box-sizing: border-box; height: 300px; width: 428.5px;" width="428"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
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
            </div>
        </div>
    <div class="row mt-4">
        <div class="col-md-6">
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


    var watchedData = @json($watchedTillPercentageTotals);

    // Extract labels and data from watchedData
    var labels = Object.keys(watchedData).map(key => key.replace('avg_watched_', '') + '%');
    var data = Object.values(watchedData);

    var ctx2 = document.getElementById("viewing-range-sum").getContext("2d");
    var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);
    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)');

    new Chart(ctx2, {
        type: "line",
        data: {
            labels: labels,  // Use extracted labels here
            datasets: [{
                label: "Watched Till Percentage",
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 0,
                borderColor: "#cb0c9f",
                backgroundColor: gradientStroke1,
                fill: true,
                data: data,  // Use extracted data here
                maxBarThickness: 6
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

    var labels = @json($labels);
    var processedStats = @json($processedStats);
    var averages = processedStats.map(stat => stat.average_viewing_percentage);

    // Bar chart
    var ctx5 = document.getElementById("average-viewing-range").getContext("2d");

    new Chart(ctx5, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: "Average viewing range of this day:",
                weight: 5,
                borderWidth: 0,
                borderRadius: 4,
                backgroundColor: '#3A416F',
                data: averages,
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
</script>
@endpush
