<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $query = Project::query();

        // Search by keyword (title, location, investor)
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('investor', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['selling', 'upcoming', 'handed_over'])) {
            $query->where('status', $request->status);
        }

        // Filter by city
        if ($request->has('city') && !empty($request->city)) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        $projects = $query->latest()->paginate(6)->withQueryString();

        return view('projects.index', compact('projects'));
    }

    /**
     * Display the specified project.
     */
    public function show($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        
        // Get approved properties belonging to this project
        $properties = $project->properties()
            ->where('status', 'approved')
            ->latest()
            ->paginate(6);

        return view('projects.show', compact('project', 'properties'));
    }
}
