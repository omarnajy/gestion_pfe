@extends('layouts.admin')

@section('page-title', 'Statistiques des PFE')

@section('admin-content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Statistiques des PFE') }}</h5>

                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Statistiques générales avec style AdminLTE -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $totalProjects }}</h3>
                                    <p>Total projets</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $totalStudents }}</h3>
                                    <p>Total étudiants</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $totalSupervisors }}</h3>
                                    <p>Total encadreurs</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $statusStats['pending'] ?? 0 }}</h3>
                                    <p>En attente</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphiques dans des cartes séparées -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('Projets par statut') }}</h3>
                                </div>
                                <div class="card-body" style="height: 300px;">
                                    <div class="chart-container" style="height: 250px;">
                                        <canvas id="projectsByStatusChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('Projets par département') }}</h3>
                                </div>
                                <div class="card-body" style="height: 300px;">
                                    <div class="chart-container" style="height: 250px;">
                                        <canvas id="projectsByFieldChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques des superviseurs -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('Charge de travail des encadreurs') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Encadreur</th>
                                                    <th>Total projets</th>
                                                    <th>Département</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($supervisors as $supervisor)
                                                    <tr>
                                                        <td>{{ $supervisor->name }}</td>
                                                        <td>
                                                            <span
                                                                class="badge badge-primary">{{ $supervisor->total_projects }}</span>
                                                        </td>
                                                        <td>{{ $supervisor->department ?? 'Non défini' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center">Aucun encadreur trouvé</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Attendre que le DOM soit complètement chargé
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== STATISTICS CHARTS INITIALIZATION ===');

            // Configuration Chart.js pour éviter les conflits
            Chart.defaults.responsive = true;
            Chart.defaults.maintainAspectRatio = false;

            // Données pour le graphique des statuts
            const statusData = {
                labels: ['En attente', 'Approuvés', 'Rejetés'],
                datasets: [{
                    data: [
                        {{ $statusStats['pending'] ?? 0 }},
                        {{ $statusStats['approved'] ?? 0 }},
                        {{ $statusStats['rejected'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#ffc107', // warning - En attente
                        '#28a745', // success - Approuvés
                        '#dc3545' // danger - Rejetés
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            };

            console.log('Status Data:', statusData);

            // Créer le graphique des statuts
            const statusCanvas = document.getElementById('projectsByStatusChart');
            if (statusCanvas) {
                const statusCtx = statusCanvas.getContext('2d');
                console.log('Creating status chart...');

                new Chart(statusCtx, {
                    type: 'pie',
                    data: statusData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed + ' projets';
                                        return label;
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: 10
                        }
                    }
                });
                console.log('Status chart created successfully');
            } else {
                console.error('Status canvas element not found!');
            }

            // Données pour le graphique des filières
            const fieldLabels = {!! json_encode($fieldStats['labels'] ?? []) !!};
            const fieldDataValues = {!! json_encode($fieldStats['data'] ?? []) !!};

            console.log('Field Labels:', fieldLabels);
            console.log('Field Data:', fieldDataValues);

            const fieldData = {
                labels: fieldLabels,
                datasets: [{
                    data: fieldDataValues,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                        '#6610f2', '#fd7e14', '#20c997', '#6c757d', '#e83e8c'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            };

            // Créer le graphique des filières
            const fieldCanvas = document.getElementById('projectsByFieldChart');
            if (fieldCanvas) {
                const fieldCtx = fieldCanvas.getContext('2d');
                console.log('Creating field chart...');

                new Chart(fieldCtx, {
                    type: 'pie',
                    data: fieldData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed + ' projets';
                                        return label;
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: 10
                        }
                    }
                });
                console.log('Field chart created successfully');
            } else {
                console.error('Field canvas element not found!');
            }

            console.log('=== CHARTS INITIALIZATION COMPLETE ===');
        });

        function changeAcademicYear(year) {
            window.location.href = "{{ route('admin.statistics') }}?year=" + year;
        }
    </script>
@endpush
