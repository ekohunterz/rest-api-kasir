<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query();

        // Filter berdasarkan pencarian jika ada
        if (request()->has('search')) {
            $categories->where('name', 'like', '%' . request('search') . '%');
        }

        // Menggunakan paginate jika ada parameter page, jika tidak, ambil semua data
        $result = request()->has('page') && request('page') != 0 ? $categories->paginate(10) : $categories->get();

        return new CategoryResource($result);
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        $category = new Category();
        $category->name = $request->name;

        $category->save();

        return new CategoryResource($category);
    }

    public function show($id)
    {

        $category = Category::findOrFail($id);

        return new CategoryResource($category);
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id
        ]);

        $category = Category::findOrFail($id);

        $category->name = $request->name;

        $category->save();

        return new CategoryResource($category);
    }

    public function destroy($id)
    {

        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json(null, 204);
    }
}
