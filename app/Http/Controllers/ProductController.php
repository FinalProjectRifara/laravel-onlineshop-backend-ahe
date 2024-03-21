<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // index
    public function index(Request $request)
    {
        // $products = Product::where('name', 'like', "%$request->search%")
        //     ->orderBy('id', 'desc')
        //     ->paginate(5);
        // return view('pages.product.index', compact('products'));

        // Search
        $query = Product::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Eager load the 'category' relationship
        $products = $query->with('category')->paginate(5);

        // Fetch categories regardless of the search
        $categories = \App\Models\Category::all();

        return view('pages.product.index', compact('products', 'categories'));
    }

    // create
    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('pages.product.create', compact('categories'));
    }

    // store
    public function store(ProductRequest $request)
    {
        try {
            $data = $request->all();
            if ($request->image != null) {
                $filename = time() . '.' . $request->image->extension();
                $request->image->storeAs('public/products', $filename);
                $data['image'] = $filename;
            }

            Product::create($data);
            return redirect()->route('product.index')->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something goes wrong while uploading file!');
        }
    }



    // show
    public function show($id)
    {
        return  view('pages.dashboard');
    }

    // edit
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $dataCategory = Category::all();
        // dd(asset('storage/products/' . $product->image));

        return view('pages.product.edit', [
            'dataCategory' => $dataCategory,
            'product' => $product,
        ]);
    }

    // update
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->category_id = $request->input('category_id');
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        // $filename = time() . '.' . $request->image->extension();
        if ($request->image == null) {
            $product->fill($request->except('image'));
        } else if ($request->image != null) {
            Storage::delete('public/products/' . $product->image);
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/products', $filename);
            $product->image = $filename;
        }
        $product->price = $request->input('price');
        $product->weight = $request->input('weight');
        $product->stock = $request->input('stock');
        if ($request->input('is_available') == "on") {
            $product->is_available = true;
        } else {
            $product->is_available = false;
        }
        $product->save();
        return redirect()->route('product.index')->with('success', 'Product updated successfully');
    }

    // destroy
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        Storage::delete('public/products/' . $product->image);

        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product deleted successfully!');
    }
}
