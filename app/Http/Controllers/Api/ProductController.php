<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        // Get products, either all or search by category_id or name
        $products = Product::query(); // Memulai query builder untuk model Product

        if ($request->has('category_id')) {
            $products->where('category_id', $request->category_id); // Menambahkan filter berdasarkan category_id jika disediakan dalam request
        }

        if ($request->has('name')) {
            $products->where('name', 'like', '%' . $request->name . '%'); // Menambahkan filter berdasarkan nama jika disediakan dalam request
        }

        // Melakukan paginasi dengan batas yang sangat besar untuk mendapatkan semua hasil dalam satu halaman
        $products = $products->paginate(99999);

        return response()->json([
            "message" => "Success",
            "data" => $products
        ], 200); // Status HTTP 200 untuk sukses
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementasi untuk menyimpan produk baru akan ditempatkan di sini.
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Implementasi untuk menampilkan produk tertentu berdasarkan ID akan ditempatkan di sini.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementasi untuk memperbarui produk berdasarkan ID akan ditempatkan di sini.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implementasi untuk menghapus produk berdasarkan ID akan ditempatkan di sini.
    }
}
