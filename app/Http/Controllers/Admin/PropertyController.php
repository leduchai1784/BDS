<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Category;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of the properties.
     */
    public function index(Request $request)
    {
        $query = Property::query()->with(['agent', 'category']);

        // Search by keyword in title or address
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $properties = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('admin.properties.index', compact('properties', 'categories'));
    }

    /**
     * Display the specified property details.
     */
    public function show($id)
    {
        $property = Property::with(['agent', 'category'])->findOrFail($id);
        return view('admin.properties.show', compact('property'));
    }

    /**
     * Update the status (approve, hide, reject) of the property.
     */
    public function updateStatus(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,approved,hidden,rejected'
        ]);

        $property->status = $request->input('status');
        $property->save();

        $statusLabels = [
            'approved' => 'Duyệt đăng tin',
            'hidden' => 'Ẩn tin đăng',
            'rejected' => 'Từ chối tin đăng',
            'pending' => 'Chuyển về chờ duyệt'
        ];

        return back()->with('success', 'Đã cập nhật trạng thái tin đăng thành công sang: ' . $statusLabels[$property->status]);
    }

    /**
     * Remove the specified property.
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return redirect()->route('admin.properties.index')->with('success', 'Xóa tin đăng bất động sản thành công!');
    }
}
