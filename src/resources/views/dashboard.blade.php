@extends('layouts.user_type.auth')

@section('content')

  <div class="row">
      <!-- Dynamically Generated Statistics Cards -->
      @php
          $stats = [
            'totalProductions' => ['title' => "Total Productions", 'icon' => "ni-tv-2"],
            'viewsLastDay' => ['title' => "Viewers Last Day", 'icon' => "ni-chart-bar-32"],
            'viewsLastWeek' => ['title' => "Viewers Last Week", 'icon' => "ni-chart-bar-32"],
            'viewsLastMonth' => ['title' => "Viewers Last Month", 'icon' => "ni-chart-bar-32"],
          ];
      @endphp

      @foreach ($stats as $key => $info)
          @if (isset($topRowStatistics[$key]))
              <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                  <div class="card">
                      <div class="card-body p-3">
                          <div class="row">
                              <div class="col-8">
                                  <div class="numbers">
                                      <p class="text-sm mb-0 text-capitalize font-weight-bold">{{ $info['title'] }}</p>
                                      <h5 class="font-weight-bolder mb-0">
                                          {{ $topRowStatistics[$key]['value'] }}
                                          @if (isset($topRowStatistics[$key]['change']) && !empty($topRowStatistics[$key]['change']))
                                            <span class="text-{{ $topRowStatistics[$key]['status'] }} text-sm font-weight-bolder">{{ $topRowStatistics[$key]['change'] }}%</span>
                                          @endif
                                      </h5>
                                  </div>
                              </div>
                              <div class="col-4 text-end">
                                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                      <i class="ni {{ $info['icon'] }} text-lg opacity-10" aria-hidden="true"></i>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          @endif
      @endforeach
  </div>
  <div class="row mt-4">
    <div class="col-12">
      <div class="card z-index-2">
        <div class="card-header pb-0">
          <h6>Total viewers last year</h6>
        </div>
        <div class="card-body p-3">
          <div class="chart">
            <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row my-4">
      <div class="col-lg-4 col-md-12 mb-md-0 mb-4">
          <div class="card">
              <div class="card-header pb-0">
                  <div class="row">
                      <div class="col-12">
                          <h6>Top 10 Newest Productions</h6>
                      </div>
                  </div>
              </div>
              <div class="card-body px-0 pb-2">
                  <div class="table-responsive">
                      <table class="table align-items-center mb-0">
                          <thead>
                          <tr>
                              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Title</th>
                              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Viewers</th>
                          </tr>
                          </thead>
                          <tbody>
                          @foreach ($detailedTop10Newest as $production)
                              <tr onclick="window.location='/production/{{ $production->id }}/analyse';" style="cursor: pointer;">
                                  <td>{{ $production->title }}</td>
                                  <td>{{ $production->description }}</td>
                                  <td class="text-center">{{ $production->viewers }}</td>
                              </tr>
                          @endforeach
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-lg-4 col-md-12 mb-md-0 mb-4">
          <div class="card">
              <div class="card-header pb-0">
                  <div class="row">
                      <div class="col-12">
                          <h6>Top 10 best Productions</h6>
                      </div>
                  </div>
              </div>
              <div class="card-body px-0 pb-2">
                  <div class="table-responsive">
                      <table class="table align-items-center mb-0">
                          <thead>
                          <tr>
                              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Title</th>
                              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Viewers</th>
                          </tr>
                          </thead>
                          <tbody>
                          @foreach ($detailedLast10Best as $production)
                              <tr onclick="window.location='/production/{{ $production->id }}/analyse';" style="cursor: pointer;">
                                  <td>{{ $production->title }}</td>
                                  <td>{{ $production->description }}</td>
                                  <td class="text-center">{{ $production->viewers }}</td>
                              </tr>
                          @endforeach
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-lg-4 col-md-12 mb-md-0 mb-4">
          <div class="card">
              <div class="card-header pb-0">
                  <div class="row">
                      <div class="col-12">
                          <h6>Top 10 worst Productions</h6>
                      </div>
                  </div>
              </div>
              <div class="card-body px-0 pb-2">
                  <div class="table-responsive">
                      <table class="table align-items-center mb-0">
                          <thead>
                          <tr>
                              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Title</th>
                              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Viewers</th>
                          </tr>
                          </thead>
                          <tbody>
                          @foreach ($detailedTop10Worst as $production)
                              <tr onclick="window.location='/production/{{ $production->id }}/analyse';" style="cursor: pointer;">
                                  <td>{{ $production->title }}</td>
                                  <td>{{ $production->description }}</td>
                                  <td class="text-center">{{ $production->viewers }}</td>
                              </tr>
                          @endforeach
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
  </div>

@endsection
@push('dashboard')
  <script>
    var viewsPerMonth = @json($viewsPerMonth);
    window.onload = function() {

      var ctx = document.getElementById("chart-line").getContext("2d");

      var gradientStroke1 = ctx.createLinearGradient(0, 230, 0, 50);

      gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
      gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
      gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

      var gradientStroke2 = ctx.createLinearGradient(0, 230, 0, 50);

      gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
      gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
      gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

    // Convert Laravel collection to usable chart data
    var labels = viewsPerMonth.map(function(view) {
        return view.month_name; // Assuming `month_name` is the format "Jan", "Feb", etc.
    });

    var data = viewsPerMonth.map(function(view) {
        return view.total_views;
    });

    new Chart(ctx, {
        type: "line",
        data: {
            labels: labels, // Use dynamic labels
            datasets: [{
                label: "Total Views",
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 0,
                borderColor: "#2da9ca",
                backgroundColor: gradientStroke1,
                fill: true,
                data: data, // Use dynamic data
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
                padding: 20,
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
    }
  </script>
@endpush

