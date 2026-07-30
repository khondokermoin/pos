<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('company_id', Auth::user()->company_id)->latest()->get();
        return view('company.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('company.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,company_id,' . Auth::user()->company_id,
        ]);

        Category::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
        ]);

        return redirect()->route('company.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        if ($category->company_id !== Auth::user()->company_id) {
            abort(403);
        }
        return view('company.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if ($category->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id . ',id,company_id,' . Auth::user()->company_id,
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('company.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        if ($category->products()->count() > 0) {
            return redirect()->route('company.categories.index')->with('error', 'Cannot delete category. It has associated products.');
        }

        $category->delete();
        return redirect()->route('company.categories.index')->with('success', 'Category deleted successfully.');
    }
}