<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Appointment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $totalAccounts = User::count();
        $totalOwners = User::where('role', 'owner')->count();
        $totalTenants = User::where('role', 'tenant')->count();
        $totalProperties = Property::count();
        $totalAppointments = Appointment::count();
        $totalViews = Property::sum('views_count');

        // Fetch recent pending properties awaiting approval
        $pendingProperties = Property::where('status', 'pending')
            ->with(['agent', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // Fetch recent appointments
        $recentAppointments = Appointment::with('property')
            ->latest()
            ->take(5)
            ->get();

        // Calculate 6-month chart data
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = \Carbon\Carbon::now()->subMonths($i)->format('m/Y');
        }

        $propertiesCounts = array_fill_keys($months, 0);
        $usersCounts = array_fill_keys($months, 0);
        $appointmentsCounts = array_fill_keys($months, 0);

        $properties = Property::where('created_at', '>=', \Carbon\Carbon::now()->subMonths(6))->get();
        foreach ($properties as $property) {
            $month = $property->created_at->format('m/Y');
            if (isset($propertiesCounts[$month])) {
                $propertiesCounts[$month]++;
            }
        }

        $users = User::where('created_at', '>=', \Carbon\Carbon::now()->subMonths(6))->get();
        foreach ($users as $user) {
            $month = $user->created_at->format('m/Y');
            if (isset($usersCounts[$month])) {
                $usersCounts[$month]++;
            }
        }

        $appointments = Appointment::where('created_at', '>=', \Carbon\Carbon::now()->subMonths(6))->get();
        foreach ($appointments as $appointment) {
            $month = $appointment->created_at->format('m/Y');
            if (isset($appointmentsCounts[$month])) {
                $appointmentsCounts[$month]++;
            }
        }

        $chartLabels = array_keys($propertiesCounts);
        $propertiesData = array_values($propertiesCounts);
        $usersData = array_values($usersCounts);
        $appointmentsData = array_values($appointmentsCounts);

        return view('admin.dashboard', compact(
            'totalAccounts',
            'totalOwners',
            'totalTenants',
            'totalProperties',
            'totalAppointments',
            'totalViews',
            'pendingProperties',
            'recentAppointments',
            'chartLabels',
            'propertiesData',
            'usersData',
            'appointmentsData'
        ));
    }
}
