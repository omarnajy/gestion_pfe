@extends('layouts.admin')

@section('admin-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Statistiques des PFE') }}</h5>
                    <div>
                        <select id="academic-year" class="form-select" onchange="changeAcademicYear(this.value)">
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    Année {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Statistiques générales -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $totalProjects }}</h4>
                                            <p class="mb-0">Total projets</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-project-diagram fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $totalStudents }}</h4>
                                            <p class="mb-0">Total étudiants</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $totalSupervisors }}</h4>
                                            <p class="mb-0">Total encadreurs</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $statusStats['pending'] ?? 0 }}</h4>
                                            <p class="mb-0">En attente</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphiques -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ __('Projets par statut') }}</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="projectsByStatusChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ __('Projets par département') }}</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="projectsByFieldChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques des superviseurs -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ __('Charge de travail des encadreurs') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
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
                                                    <td>{{ $supervisor->total_projects }}</td>
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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== DEBUG START ===');
        console.log('Status Stats:', @json($statusStats));
        console.log('Field Stats:', @json($fieldStats));

        // Données pour le graphique des statuts - TOUJOURS CRÉER LE GRAPHIQUE
        const statusData = [
            {{ $statusStats['pending'] ?? 0 }}, 
            {{ $statusStats['approved'] ?? 0 }},
            {{ $statusStats['rejected'] ?? 0 }}
        ];
        
        console.log('Status Data:', statusData);

        // Créer le graphique des statuts - FORCER LA CRÉATION
        const statusCanvas = document.getElementById('projectsByStatusChart');
        if (statusCanvas) {
            console.log('Creating status chart...');
            new Chart(statusCanvas, {
                type: 'pie',
                data: {
                    labels: ['En attente', 'Approuvés', 'Rejetés'],
                    datasets: [{
                        data: statusData,
                        backgroundColor: [
                            '#ffc107', // warning - En attente
                            '#28a745', // success - Approuvés
                            '#dc3545'  // danger - Rejetés
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
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
                    }
                }
            });
            console.log('Status chart created successfully');
        } else {
            console.error('Status canvas not found!');
        }

        // Données pour le graphique des filières - TOUJOURS CRÉER LE GRAPHIQUE
        const fieldLabels = {!! json_encode($fieldStats['labels'] ?? []) !!};
        const fieldDataValues = {!! json_encode($fieldStats['data'] ?? []) !!};
        
        console.log('Field Labels:', fieldLabels);
        console.log('Field Data:', fieldDataValues);

        // Créer le graphique des filières - FORCER LA CRÉATION
        const fieldCanvas = document.getElementById('projectsByFieldChart');
        if (fieldCanvas) {
            console.log('Creating field chart...');
            new Chart(fieldCanvas, {
                type: 'pie',
                data: {
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
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
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
                    }
                }
            });
            console.log('Field chart created successfully');
        } else {
            console.error('Field canvas not found!');
        }

        console.log('=== DEBUG END ===');
    });

    function changeAcademicYear(year) {
        window.location.href = "{{ route('admin.statistics') }}?year=" + year;
    }
</script>
@endpush