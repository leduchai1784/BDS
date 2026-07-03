<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    /**
     * Display a listing of agents/owners.
     */
    public function index(Request $request)
    {
        // Fetch active users with the role of 'owner' (agents/owners)
        $query = User::where('role', 'owner')->where('status', 'active');

        // Filter by type: company or agent
        if ($request->has('type') && !empty($request->type)) {
            if ($request->type === 'company') {
                $query->whereNotNull('company')->where('company', '!=', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('company')->orWhere('company', '');
                });
            }
        }

        // Search by name, phone, or company
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by location
        if ($request->has('location') && !empty($request->location)) {
            $loc = $request->location;
            $query->where(function ($q) use ($loc) {
                $q->where('add_province', 'like', "%{$loc}%")
                  ->orWhere('add_district', 'like', "%{$loc}%");
            });
        }

        $agents = $query->latest()->paginate(8)->withQueryString();

        return view('agents.index', compact('agents'));
    }

    /**
     * Display the agent details and their properties.
     */
    public function show($id)
    {
        $agent = User::where('role', 'owner')->where('status', 'active')->findOrFail($id);

        // Fetch properties posted by this agent
        $propertiesQuery = $agent->properties()->where('status', 'approved');

        // Split into sale and rent properties
        $saleProperties = (clone $propertiesQuery)->where('transaction_type', 'sale')->latest()->get();
        $rentProperties = (clone $propertiesQuery)->where('transaction_type', 'rent')->latest()->get();

        return view('agents.show', compact('agent', 'saleProperties', 'rentProperties'));
    }
}
