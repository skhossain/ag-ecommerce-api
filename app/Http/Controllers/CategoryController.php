<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Category::all();
    }
    public function categoryList(){
        return Category::paginate(10);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories',
        ]);

        return Category::create([
            'name' => $request->name,
            'slug' => $this->generateCategoryUniqueSlug($request->name),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,'.$id,
        ]); 

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'slug' => $this->generateCategoryUniqueSlug($request->name),
        ]);

        return $category;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    function generateCategoryUniqueSlug($name)
{
    // Convert name to slug format
    $slug = Str::slug($name);
    $originalSlug = $slug;
    $count = 1;

    // Check if slug already exists in the database
    while (Category::where('slug', $slug)->exists()) {
        $slug = "{$originalSlug}-{$count}";
        $count++;
    }

    return $slug;
}
}
