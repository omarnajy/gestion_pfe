@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Projets par encadreur') }}</h5>
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
                        <div class="col-md-12">
                            <div class="chart-container" style="position: relative; height:400px;">
                                <canvas id="supervisorProjectsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Encadreur') }}</th>
                                            <th>{{ __('Département') }}</th>
                                            <th class="text-center">{{ __('Projets total') }}</th>
                                            <th class="text-center">{{ __('En cours') }}</th>
                                            <th class="text-center">{{ __('Terminés') }}</th>
                                            <th class="text-center">{{ __('Rejetés') }}</th>
                                            <th class="text-center">{{ __('Note moyenne') }}</th>
                                            <th class="text-center">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($supervisors as $supervisor)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        @if($supervisor->profile_photo)
                                                            <img src="{{ asset('storage/' . $supervisor->profile_photo) }}" alt="{{ $supervisor->name }}" class="rounded-circle" style="width: 40px; height: 40px;">
                                                        @else
                                                            <div class="avatar-placeholder bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                                                {{ substr($supervisor->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div>{{ $supervisor->name }}</div>
                                                        <small class="text-muted">{{ $supervisor->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $supervisor->department }}</td>
                                            <td class="text-center">{{ $supervisor->projects_count }}</td>
                                            <td class="text-center">{{ $supervisor->pending_projects_count }}</td>
                                            <td class="text-center">{{ $supervisor->completed_projects_count }}</td>
                                            <td class="text-center">{{ $supervisor->rejected_projects_count }}</td>
                                            <td class="text-center">
                                                @if($supervisor->average_rating)
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        {{ number_format($supervisor->average_rating, 1) }}
                                                        <div class="ms-2">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= round($supervisor->average_rating))
                                                                    <i class="fas fa-star text-warning"></i>
                                                                @else
                                                                    <i class="far fa-star text-warning"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('supervisor.projects.list', $supervisor->id) }}?year={{ $selectedYear }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-list"></i> {{ __('Voir projets') }}
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
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

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('supervisorProjectsChart').getContext('2d');
        
        const data = {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: 'En cours',
                    data: {!! json_encode($chartData['pending']) !!},
                    backgroundColor: '#ffc107',
                    borderColor: '#ffc107',
                    borderWidth: 1
                },
                {
                    label: 'Terminés',
                    data: {!! json_encode($chartData['completed']) !!},
                    backgroundColor: '#28a745',
                    borderColor: '#28a745',
                    borderWidth: 1
                },
                {
                    label: 'Rejetés',
                    data: {!! json_encode($chartData['rejected']) !!},
                    backgroundColor: '#dc3545',
                    borderColor: '#dc3545',
                    borderWidth: 1
                }
            ]
        };
        
        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distribution des projets par encadreur'
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        };
        
        new Chart(ctx, config);
    });

    function changeAcademicYear(year) {
        window.location.href = "{{ route('admin.statistics.projects-by-supervisor') }}?year=" + year;
    }

    document.getElementById('exportData').addEventListener('click', function() {
        window.location.href = "{{ route('admin.statistics.projects-by-supervisor.export') }}?year={{ $selectedYear }}";
    });
</script>
@endsection