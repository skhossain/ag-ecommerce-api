<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    function generateProductUniqueSlug($name){
        // Convert name to slug format
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        // Check if slug already exists in the database
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
    // Fetch all products
    public function index(Request $request)
    {
        // Add some query scopes to filter products
        $products = Product::when($request->search, function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%");
        })->with('category')->paginate(10);
        return $products;
    }

    public function productList(){
        $products = Product::with('category')->paginate(10);
        return $products;
    }

    // Fetch a single product
    public function show(Product $product)
    {
        return $product;
    }

    // Create a new product (admin only)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Validate image
            'status' => 'required|in:active,inactive', // Validate status
        ]);
    
        // Upload Image
        $imagePath = $request->file('image')->store('products', 'public');
        $slug = $this->generateProductUniqueSlug($request->name);
        // Create Product
        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'image_url' => "storage/{$imagePath}",
            'status' => $request->status,
        ]);
    
        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    // Update a product (admin only)
    public function update(Request $request, Product $product)
{
    // Validate the request data
    $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'description' => 'sometimes|required|string',
        'price' => 'sometimes|required|numeric|min:0',
        'category_id' => 'sometimes|required|exists:categories,id',
        'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'status' => 'sometimes|required|in:active,inactive',
        'is_deleted' => 'sometimes|required|boolean',
    ]);
    if($request->hasFile('image')){
        // Upload Image
        $imagePath = $request->file('image')->store('products', 'public');
        $product->update([
            'image_url' => asset("storage/{$imagePath}"), // Store full image URL
        ]);
    }
    // Update the product data
    $product->update([
        'name' => $request->input('name', $product->name),
        'description' => $request->input('description', $product->description),
        'price' => $request->input('price', $product->price),
        'category_id' => $request->input('category_id', $product->category_id),
        'status' => $request->input('status', $product->status),
        'is_deleted' => $request->input('is_deleted', $product->is_deleted),
    ]);

    // Return the updated product
    return $product;
}

    // Delete a product (admin only)
    public function destroy(Product $product)
    {
        $product->is_delete = true;
        $product->save();
        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }

    
}
