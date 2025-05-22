@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Projets par statut') }}</h5>
                    <div class="d-flex">
                        <a href="{{ route('admin.statistics') }}" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left"></i> {{ __('Retour aux statistiques') }}
                        </a>
                        <button class="btn btn-sm btn-outline-primary" id="exportData">
                            <i class="fas fa-download"></i> {{ __('Exporter') }}
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-end mb-3">
                                <div class="form-inline">
                                    <label for="academic-year" class="me-2">Année académique:</label>
                                    <select id="academic-year" class="form-select form-select-sm" style="width: 200px;" onchange="changeAcademicYear(this.value)">
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                {{ $year }}-{{ $year + 1 }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Distribution des projets') }}</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusDistributionChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Évolution des statuts par mois') }}</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusTimelineChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Statistiques par statut') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($statusStats as $status => $data)
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100 bg-light">
                                                    <div class="card-body text-center">
                                                        <div class="status-icon mb-3">
                                                            @if($status == 'pending')
                                                                <i class="fas fa-clock fa-2x text-warning"></i>
                                                            @elseif($status == 'approved')
                                                                <i class="fas fa-check-circle fa-2x text-info"></i>
                                                            @elseif($status == 'in_progress')
                                                                <i class="fas fa-spinner fa-2x text-primary"></i>
                                                            @elseif($status == 'completed')
                                                                <i class="fas fa-trophy fa-2x text-success"></i>
                                                            @elseif($status == 'rejected')
                                                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                                                            @endif
                                                        </div>
                                                        <h4>{{ $data['count'] }}</h4>
                                                        <h6>{{ $data['label'] }}</h6>
                                                        <p class="text-muted mb-0">{{ $data['percentage'] }}% des projets</p>
                                                        @if($status == 'completed')
                                                            <div class="mt-2 small">
                                                                <strong>Note moyenne:</strong> {{ number_format($data['avg_rating'], 1) }}/5
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-footer">
                                                        <a href="{{ route('admin.projects') }}?status={{ $status }}&year={{ $selectedYear }}" class="btn btn-sm btn-outline-secondary d-block">
                                                            Voir les projets
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ __('Durée moyenne de complétion par département') }}</h6>
                                    <div>
                                        <select id="status-filter" class="form-select form-select-sm" onchange="filterByStatus(this.value)">
                                            <option value="all">Tous les statuts</option>
                                            <option value="completed">Terminés uniquement</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="position: relative; height:350px;">
                                        <canvas id="completionTimeChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Distribution par statut
        const ctxDistribution = document.getElementById('statusDistributionChart').getContext('2d');
        const distributionChart = new Chart(ctxDistribution, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_column($statusStats, 'label')) !!},
                datasets: [{
                    data: {!! json_encode(array_column($statusStats, 'count')) !!},
                    backgroundColor: [
                        '#ffc107', // warning - pending
                        '#17a2b8', // info - approved
                        '#007bff', // primary - in_progress
                        '#28a745', // success - completed
                        '#dc3545'  // danger - rejected
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        // Évolution par mois
        const ctxTimeline = document.getElementById('statusTimelineChart').getContext('2d');
        const timelineChart = new Chart(ctxTimeline, {
            type: 'line',
            data: {
                labels: {!! json_encode($timelineData['months']) !!},
                datasets: [{
                    label: 'En attente',
                    data: {!! json_encode($timelineData['pending']) !!},
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    borderColor: '#ffc107',
                    borderWidth: 2,
                    tension: 0.3
                },
                {
                    label: 'En cours',
                    data: {!! json_encode($timelineData['in_progress']) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderColor: '#007bff',
                    borderWidth: 2,
                    tension: 0.3
                },
                {
                    label: 'Terminés',
                    data: {!! json_encode($timelineData['completed']) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: '#28a745',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Durée moyenne de complétion
        const ctxCompletion = document.getElementById('completionTimeChart').getContext('2d');
        const completionChart = new Chart(ctxCompletion, {
            type: 'bar',
            data: {
                labels: {!! json_encode($completionTimeData['departments']) !!},
                datasets: [{
                    label: 'Nombre de jours (moyenne)',
                    data: {!! json_encode($completionTimeData['avg_days']) !!},
                    backgroundColor: 'rgba(23, 162, 184, 0.7)',
                    borderColor: '#17a2b8',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jours moyens'
                        }
                    }
                }
            }
        });
    });

    function changeAcademicYear(year) {
        window.location.href = "{{ route('admin.statistics.projects-by-status') }}?year=" + year;
    }

    function filterByStatus(status) {
        window.location.href = "{{ route('admin.statistics.projects-by-status') }}?year={{ $selectedYear }}&filter=" + status;
    }

    document.getElementById('exportData').addEventListener('click', function() {
        window.location.href = "{{ route('admin.statistics.projects-by-status.export') }}?year={{ $selectedYear }}";
    });
</script>
@endsection