@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Statistiques - Projets par domaine') }}</span>
                    <a href="{{ route('admin.statistics') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Retour aux statistiques') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-7">
                            <canvas id="projectsByFieldChart" height="350"></canvas>
                        </div>
                        <div class="col-md-5">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Domaine') }}</th>
                                            <th class="text-center">{{ __('Nombre de projets') }}</th>
                                            <th class="text-center">{{ __('Pourcentage') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = $projectsByField->sum('count'); @endphp
                                        @foreach($projectsByField as $field)
                                        <tr>
                                            <td>{{ $field->field }}</td>
                                            <td class="text-center">{{ $field->count }}</td>
                                            <td class="text-center">
                                                @if($total > 0)
                                                    {{ number_format(($field->count / $total) * 100, 1) }}%
                                                @else
                                                    0%
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td>{{ __('Total') }}</td>
                                            <td class="text-center">{{ $total }}</td>
                                            <td class="text-center">100%</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>{{ __('Tendances par année académique') }}</h5>
                            <canvas id="fieldTrendChart" height="250"></canvas>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>{{ __('Répartition des notes par domaine') }}</h5>
                            <canvas id="fieldGradesChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Projets par domaine (Pie Chart)
    const fieldCtx = document.getElementById('projectsByFieldChart').getContext('2d');
    const fieldColors = [
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 99, 132, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(255, 159, 64, 0.7)',
        'rgba(199, 199, 199, 0.7)',
        'rgba(83, 102, 255, 0.7)',
        'rgba(40, 159, 64, 0.7)',
        'rgba(210, 199, 199, 0.7)',
    ];
    
    new Chart(fieldCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($projectsByField->pluck('field')) !!},
            datasets: [{
                data: {!! json_encode($projectsByField->pluck('count')) !!},
                backgroundColor: fieldColors,
                borderColor: fieldColors.map(color => color.replace('0.7', '1')),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                title: {
                    display: true,
                    text: '{{ __("Répartition des projets par domaine") }}'
                }
            }
        }
    });
    
    // Tendances par année (Line Chart)
    const trendCtx = document.getElementById('fieldTrendChart').getContext('2d');
    const years = {!! json_encode($yearlyTrends->pluck('year')->unique()) !!};
    const fields = {!! json_encode($projectsByField->pluck('field')) !!};
    
    const trendDatasets = fields.map((field, index) => {
        return {
            label: field,
            data: years.map(year => {
                const match = {!! json_encode($yearlyTrends) !!}.find(t => t.year == year && t.field == field);
                return match ? match.count : 0;
            }),
            borderColor: fieldColors[index % fieldColors.length].replace('0.7', '1'),
            backgroundColor: fieldColors[index % fieldColors.length],
            tension: 0.1,
            fill: false
        };
    });
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: years,
            datasets: trendDatasets
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: '{{ __("Évolution des domaines par année académique") }}'
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: '{{ __("Nombre de projets") }}'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: '{{ __("Année académique") }}'
                    }
                }
            }
        }
    });
    
    // Notes moyennes par domaine (Bar Chart)
    const gradesCtx = document.getElementById('fieldGradesChart').getContext('2d');
    
    new Chart(gradesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($fieldGrades->pluck('field')) !!},
            datasets: [{
                label: '{{ __("Note moyenne (sur 20)") }}',
                data: {!! json_encode($fieldGrades->pluck('average_grade')) !!},
                backgroundColor: fieldColors.map(color => color.replace('0.7', '0.6')),
                borderColor: fieldColors.map(color => color.replace('0.7', '1')),
                borderWidth: 1
            }],
            datasets: [{
                label: '{{ __("Taux de réussite (%)") }}',
                data: {!! json_encode($fieldGrades->pluck('success_rate')) !!},
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: '{{ __("Performance par domaine") }}'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: '{{ __("Note moyenne") }}'
                    },
                    max: 20
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: '{{ __("Taux de réussite (%)") }}'
                    },
                    max: 100,
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection('0.7', '0.6')),
                borderColor: fieldColors.map(color => color.replace