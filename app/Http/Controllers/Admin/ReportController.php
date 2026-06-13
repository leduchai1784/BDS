<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display report statistics.
     */
    public function index()
    {
        // Define the last 6 months labels
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->format('m/Y');
        }

        // Initialize empty charts arrays
        $propertiesCounts = array_fill_keys($months, 0);
        $usersCounts = array_fill_keys($months, 0);
        $appointmentsCounts = array_fill_keys($months, 0);

        // Fetch properties created in the last 6 months
        $properties = Property::where('created_at', '>=', Carbon::now()->subMonths(6))->get();
        foreach ($properties as $property) {
            $month = $property->created_at->format('m/Y');
            if (isset($propertiesCounts[$month])) {
                $propertiesCounts[$month]++;
            }
        }

        // Fetch new users registered in the last 6 months
        $users = User::where('created_at', '>=', Carbon::now()->subMonths(6))->get();
        foreach ($users as $user) {
            $month = $user->created_at->format('m/Y');
            if (isset($usersCounts[$month])) {
                $usersCounts[$month]++;
            }
        }

        // Fetch appointments scheduled in the last 6 months
        $appointments = Appointment::where('created_at', '>=', Carbon::now()->subMonths(6))->get();
        foreach ($appointments as $appointment) {
            $month = $appointment->created_at->format('m/Y');
            if (isset($appointmentsCounts[$month])) {
                $appointmentsCounts[$month]++;
            }
        }

        // Convert data to ordered lists for Chart.js
        $chartLabels = array_keys($propertiesCounts);
        $propertiesData = array_values($propertiesCounts);
        $usersData = array_values($usersCounts);
        $appointmentsData = array_values($appointmentsCounts);

        // Fetch top 10 most viewed properties
        $topProperties = Property::with(['agent', 'category'])
            ->orderBy('views_count', 'desc')
            ->take(10)
            ->get();

        // Fetch top 10 owners with most listings
        $topOwners = User::where('role', 'owner')
            ->withCount('properties')
            ->orderBy('properties_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'chartLabels',
            'propertiesData',
            'usersData',
            'appointmentsData',
            'topProperties',
            'topOwners'
        ));
    }
}
