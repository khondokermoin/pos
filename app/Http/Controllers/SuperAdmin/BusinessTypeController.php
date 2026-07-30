<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessTypeController extends Controller
{
    public function index()
    {
        $businessTypes = BusinessType::latest()->paginate(15);
        return view('super-admin.business-types.index', compact('businessTypes'));
    }

    public function create()
    {
        return view('super-admin.business-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:business_types,name',
            'slug' => 'nullable|string|max:255|unique:business_types,slug',
        ]);

        BusinessType::create([
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('superadmin.business-types.index')
            ->with('success', 'Business Type created successfully!');
    }

    public function destroy(BusinessType $businessType)
    {
        $businessType->delete();
        return back()->with('success', 'Business Type deleted successfully!');
    }
}
