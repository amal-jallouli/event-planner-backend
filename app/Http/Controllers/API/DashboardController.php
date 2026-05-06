<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        // Events by status as key=>value object
        $eventsByStatus = Event::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Registrations by month (current year)
        $registrationsByMonth = Registration::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Events by category
        $eventsByCategory = Category::withCount('events')
            ->get()
            ->map(fn($c) => [
                'name'         => $c->name,
                'events_count' => $c->events_count,
            ]);

        return response()->json([
            'total_events'           => Event::count(),
            'total_categories'       => Category::count(),
            'total_registrations'    => Registration::count(),
            'total_users'            => User::where('role', 'user')->count(),
            'events_by_category'     => $eventsByCategory,
            'events_by_status'       => $eventsByStatus,
            'registrations_by_month' => $registrationsByMonth,
        ]);
    }
}