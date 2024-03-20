<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // index
    public function index(Request $request)
    {
        // Get users with pagination
        // $categories = \App\Models\Category::paginate(5);

        $categories = DB::table('categories')
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })->paginate(5);


        return view('pages.category.index', compact('categories'));
    }

    // create
    function create()
    {
        return view('pages.category.create');
    }

    // store
    // function  store(Request $request) {
    //     $data = $request->all();
    //     // $data['password'] = Hash::make($request->input('password'));
    //     Category::create($data);
    //     return redirect()->route('category.index');
    // }

    // store
    public function store(CategoryRequest $request)
    {
        try {
            $data = $request->all();
            if ($request->image != null) {
                $filename = time() . '.' . $request->image->extension();
                $request->image->storeAs('public/categories', $filename);
                $data['image'] = $filename;
            }

            Category::create($data);
            return redirect()->route('category.index')->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something goes wrong while uploading file!');
        }
    }



    // // show
    // function show($id){
    //     return  view('pages.dashboard');
    // }

    // edit
    function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('pages.category.edit', compact('category'));
    }

    // update
    function update(Request $request, $id)
    {
        // $data = $request->all();
        // $user = Category::findOrFail($id);

        // $user->update($data);
        // return redirect()->route('category.index');

        $category = Category::findOrFail($id);
        // Name
        $category->name = $request->input('name');
        // Image
        if ($request->image == null) {
            $category->fill($request->except('image'));
        } else if ($request->image != null) {
            Storage::delete('public/categories/' . $category->image);
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/categories', $filename);
            $category->image = $filename;
        }

        $category->save();
        return redirect()->route('category.index')->with('success');
    }

    // destroy
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        Storage::delete('public/categories/' . $category->image);
        $category->delete();
        return redirect()->route('category.index');
    }
}
