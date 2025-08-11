@extends('layouts.app')
@section('title', 'Schedule Compliance')

@section('content')
    <div class="row align-items-end mb-4">
        <div class="col-md-5">
            <label for="start_date" class="form-label fw-semibold">Start Date</label>
            <input type="text" id="start_date" class="form-control shadow-sm" placeholder="Select start date">
        </div>
        <div class="col-md-5">
            <label for="end_date" class="form-label fw-semibold">End Date</label>
            <input type="text" id="end_date" class="form-control shadow-sm" placeholder="Select end date">
        </div>
        <div class="col-md-2 d-grid">
            <button id="loadGraph" class="btn btn-primary shadow-sm mt-2 mt-md-0">
                <i class="bi bi-graph-up-arrow me-1"></i> Load Graph
            </button>
        </div>
    </div>

    <canvas id="complianceChart" height="100"></canvas>

    <div id="complianceSummary" class="text-center mt-4">
        <h3>Average Daily Schedule Compliance</h3>
        <p id="dayAvg" class="mb-0">Dayshift: --%</p>
        <p id="nightAvg">Nightshift: --%</p>
    </div>
@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3"></script>
    <script>
        flatpickr("#start_date", {
            dateFormat: "d-m-Y"
        });

        flatpickr("#end_date", {
            dateFormat: "d-m-Y"
        });
        let chart;

        function loadGraph(start = null, end = null) {
            $.ajax({
                url: "{{ route('shift-log.progress-graph') }}",
                data: {
                    ...(start ? { start_date: start } : {}),
                    ...(end ? { end_date: end } : {}),
                },
                success: function (data) {
                    const labels = Object.keys(data);
                    const dayShift = labels.map(d => data[d]['day'] ?? 0);
                    const nightShift = labels.map(d => data[d]['night'] ?? 0);

                    const avg = arr => arr.reduce((a, b) => a + b, 0) / (arr.length || 1);
                    const dayAvg = avg(dayShift).toFixed(0);
                    const nightAvg = avg(nightShift).toFixed(0);

                    $('#dayAvg').text(`Dayshift ${dayAvg}%`);
                    $('#nightAvg').text(`Nightshift ${nightAvg}%`);

                    if (chart) chart.destroy();

                    const ctx = document.getElementById('complianceChart').getContext('2d');
                    chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Dayshift',
                                    data: dayShift,
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                                },
                                {
                                    label: 'Nightshift',
                                    data: nightShift,
                                    backgroundColor: 'rgba(255, 159, 64, 0.7)'
                                }
                            ]
                        },
                        options: {
                            plugins: {
                                title: {
                                    display: true,
                                    text: `Schedule Compliance (${labels[0]} - ${labels[labels.length - 1]})`,
                                    font: {
                                        size: 20
                                    }
                                },
                                annotation: {
                                    annotations: {
                                        line1: {
                                            type: 'line',
                                            yMin: 80,
                                            yMax: 80,
                                            borderColor: 'red',
                                            borderWidth: 1,
                                            label: {
                                                content: '80% Target',
                                                enabled: true,
                                                position: 'end'
                                            }
                                        }
                                    }
                                }
                            },
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    title: {
                                        display: true,
                                        text: '% Compliance'
                                    }
                                }
                            }
                        },
                        plugins: [Chart.registry.getPlugin('annotation')]
                    });
                },
                error: function (xhr) {
                    alert("Error fetching data.");
                    console.error(xhr.responseText);
                }
            });
        }

        $(document).ready(function () {
            loadGraph(); // Load default data on page load
        });

        $('#loadGraph').on('click', function () {
            const start = $('#start_date').val();
            const end = $('#end_date').val();

            if (!start || !end) {
                alert('Please select both start and end dates.');
                return;
            }

            loadGraph(start, end);
        });
    </script>
@endsection
