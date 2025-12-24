<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalionApplicants = Applicant::count();
        $byStatus = Applicant::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        
        $byPosition = Position::withCount('applicants')->get();
        
        $recentApplicants = Applicant::with('position')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => $totalionApplicants,
                'byStatus' => $byStatus,
                'byPosition' => $byPosition,
                'recent' => $recentApplicants
            ]
        ]);
    }
}
