<?php

namespace App\Http\Controllers;

use App\Services\PropertyService;
use App\Models\Project;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    /**
     * Display the homepage.
     */
    public function index()
    {
        $featured = $this->propertyService->getFeaturedProperties(8);
        $latest = $this->propertyService->getLatestProperties(4);
        $stats = $this->propertyService->getSystemStats();
        $featuredProjects = Project::latest()->take(3)->get();

        return view('home', [
            'properties' => $featured, // standard properties listing on homepage
            'latestProperties' => $latest,
            'stats' => $stats,
            'featuredProjects' => $featuredProjects
        ]);
    }

    /**
     * Display the news page.
     */
    public function news()
    {
        return view('news');
    }
}
