<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    public function getProducts()
    {
        $products = Product::with('category')->get(); // Sử dụng eager loading để tải thông tin category
        foreach ($products as $product) {
            $product->image_url = asset('images/' . $product->image);
            $product->category_name = $product->category->name; 
        }
        return response()->json($products);
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get();
        foreach ($products as $product) {
            $product->image_url = asset('images/' . $product->image);
        }
        return response()->json($products);
    }

    public function productDetail($id)
    {
        $product = Product::find($id);

        $product->image_url = asset('images/' . $product->image);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
        return response()->json($product);
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();

        $image->move(public_path('images'), $imageName);

        $product = new Product();
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        $product->image = $imageName;
        $product->save();

        return response()->json(['message' => 'Product created successfully']);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'category_id' => $request->input('category_id'),
        ]);

        return response()->json(['message' => 'Product updated successfully']);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    


}
