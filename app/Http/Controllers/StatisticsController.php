<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Supervisor;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Display statistics dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get quick stats
        $totalProjects = Project::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();
        
        $validatedProjects = Project::where('status', 'Validé')->count();
        $pendingProjects = Project::where('status', 'En attente')->count();
        $rejectedProjects = Project::where('status', 'Rejeté')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        
        // Statistiques par statut
        $statusStats = [
            'pending' => Project::where('status', 'pending')->count(),
            'approved' => Project::where('status', 'approved')->count(),
            'in_progress' => Project::where('status', 'in_progress')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'rejected' => Project::where('status', 'rejected')->count(),
        ];
        
        // Récupérer tous les statuts uniques qui existent réellement
        $actualStatuses = Project::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Statistiques par département/filière basées sur les étudiants
        $fieldStatsQuery = Project::join('users', 'projects.student_id', '=', 'users.id')
            ->select('users.department as field', DB::raw('count(*) as count'))
            ->whereNotNull('users.department')
            ->groupBy('users.department')
            ->get();
        
        $fieldStats = [
            'labels' => $fieldStatsQuery->pluck('field')->toArray(),
            'data' => $fieldStatsQuery->pluck('count')->toArray(),
        ];

        // Si pas de données de filières, créer des données basées sur les projets existants
        if (empty($fieldStats['labels']) && $totalProjects > 0) {
            // Essayer de récupérer par d'autres moyens
            $projectsWithStudentInfo = Project::with('student')->get();
            
            $fieldStats = [
                'labels' => ['Projets sans filière définie'],
                'data' => [$totalProjects],
            ];
        }


        // Années académiques
        $academicYears = [
            '2022-2023',
            '2023-2024',
            '2024-2025',
        ];
        
        $selectedYear = request()->get('year', '2024-2025');

        // Statistiques des superviseurs
        $supervisors = User::where('role', 'supervisor')
            ->withCount([
                'projectsAsSupervisor as total_projects',
                'projectsAsSupervisor as pending_projects' => function($query) {
                    $query->where('status', 'pending');
                },
                'projectsAsSupervisor as completed_projects' => function($query) {
                    $query->where('status', 'completed');
                }
            ])
            ->get();
        
        return view('admin.statistics.index', compact(
            'totalProjects', 
            'totalStudents', 
            'totalSupervisors', 
            'academicYears', 
            'selectedYear',
            'supervisors',
            'statusStats',
            'fieldStats'
        ));
    }

    /**
     * Display projects by supervisor statistics.
     *
     * @return \Illuminate\Http\Response
     */
    public function projectsBySupervisor()
    {
        $supervisors = User::where('role', 'supervisor')
            ->withCount('supervisedProjects')
            ->orderBy('supervised_projects_count', 'desc')
            ->get();
        
        // Prepare chart data
        $labels = $supervisors->pluck('name')->toArray();
        $data = $supervisors->pluck('supervised_projects_count')->toArray();
        
        return view('admin.statistics.projects-by-supervisor', compact('supervisors', 'labels', 'data'));
    }

    /**
     * Display projects by status statistics.
     *
     * @return \Illuminate\Http\Response
     */
    public function projectsByStatus()
    {
        $statusCounts = Project::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        // Prepare chart data
        $labels = $statusCounts->pluck('status')->toArray();
        $data = $statusCounts->pluck('count')->toArray();
        
        return view('admin.statistics.projects-by-status', compact('statusCounts', 'labels', 'data'));
    }
    /**
     * Display projects by field/category statistics.
     *
     * @return \Illuminate\Http\Response
     */
    public function projectsByField()
    {
        // Récupérer les projets par département des étudiants
        $fieldCounts = Project::join('users', 'projects.student_id', '=', 'users.id')
            ->select('users.department as field', DB::raw('count(*) as count'))
            ->whereNotNull('users.department')
            ->groupBy('users.department')
            ->get();
        
        // Prepare chart data
        $labels = $fieldCounts->pluck('field')->toArray();
        $data = $fieldCounts->pluck('count')->toArray();
        
        return view('admin.statistics.projects-by-field', compact('fieldCounts', 'labels', 'data'));
    }
}