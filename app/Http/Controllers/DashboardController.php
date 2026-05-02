<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLibraries = \App\Models\Library::count();
        $totalUsers = \App\Models\User::count();
        $totalStudents = \App\Models\Student::count();
        
        $revenue = \App\Models\SubscriptionHistory::where('is_paid', true)->sum('amount');
        
        $recentLibraries = \App\Models\Library::with('user')->latest()->take(5)->get();

        return response()->json([
            'stats' => [
                'total_libraries' => $totalLibraries,
                'total_users' => $totalUsers,
                'total_students' => $totalStudents,
                'total_revenue' => $revenue,
            ],
            'recent_libraries' => $recentLibraries
        ]);
    }
}
