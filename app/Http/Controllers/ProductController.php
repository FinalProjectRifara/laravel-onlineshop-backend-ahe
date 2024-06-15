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
    // Fungsi store digunakan untuk menyimpan produk baru ke dalam database setelah memproses data yang diterima dari formulir.
    public function store(ProductRequest $request)
    {
        try {
            $data = $request->all();
            if ($request->image != null) {
                // Ambil Exstensi filenya
                $filename = time() . '.' . $request->image->extension();
                // metode storeAs untuk menyimpan file:
                $request->image->storeAs('public/products', $filename);
                $data['image'] = $filename;
            }
            // Menyimpan Data ke Database:
            Product::create($data);
            // Untuk memastikan bahwa semua operasi berhasil atau tidak sama sekali (misalnya, jika penyimpanan gambar gagal, data produk tidak harus disimpan),
            // bisa menggunakan transaksi database commit dan rollback.
            DB::commit();
            return redirect()->route('product.index')->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
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

        return view('pages.product.edit', [
            'dataCategory' => $dataCategory,
            'product' => $product,
        ]);
    }

    // update
    public function update(Request $request, $id)
    {
        // Kurang efektif
        // $product = Product::findOrFail($id);
        // $product->category_id = $request->input('category_id');
        // $product->name = $request->input('name');
        // $product->description = $request->input('description');
        // if ($request->image == null) {
        //     $product->fill($request->except('image'));
        // } else if ($request->image != null) {
        //     Storage::delete('public/products/' . $product->image);
        //     $filename = time() . '.' . $request->image->extension();
        //     $request->image->storeAs('public/products', $filename);
        //     $product->image = $filename;
        // }
        // $product->price = $request->input('price');
        // $product->weight = $request->input('weight');
        // $product->stock = $request->input('stock');
        // if ($request->input('is_available') == "on") {
        //     $product->is_available = true;
        // } else {
        //     $product->is_available = false;
        // }
        // $product->save();
        // return redirect()->route('product.index')->with('success', 'Product updated successfully');

        $product = Product::findOrFail($id); // Menemukan produk berdasarkan ID atau gagal jika tidak ditemukan
        $data = $request->except('image'); // Mengambil semua data dari request kecuali 'image'

        if ($request->hasFile('image')) { // Memeriksa apakah ada file gambar yang diunggah
            Storage::delete('public/products/' . $product->image); // Menghapus gambar lama dari storage
            $filename = time() . '.' . $request->image->extension(); // Membuat nama file baru untuk gambar
            $request->image->storeAs('public/products', $filename); // Menyimpan gambar baru ke storage
            $data['image'] = $filename; // Menambahkan nama file gambar baru ke dalam data yang akan disimpan
        }

        // $data['is_available'] = $request->has('is_available') ? true : false; // Mengatur atribut 'is_available' berdasarkan checkbox

        $product->update($data); // Memperbarui produk dengan data yang diambil dari request

        return redirect()->route('product.index')->with('success', 'Product updated successfully');
    }

    // destroy
    public function destroy($id)
    {
        // $product = Product::findOrFail($id);
        // Storage::delete('public/products/' . $product->image);

        // $product->delete();

        // return redirect()->route('product.index')->with('success', 'Product deleted successfully!');

        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::delete('public/products/' . $product->image);
        }

        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product deleted successfully!');
    }
}
