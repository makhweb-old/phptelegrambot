<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Category;
use App\Translation;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();

        return response()->json($categories);
    }

    public function store(CategoryRequest $request)
    {
        $names = $request->input('names');

        $category = new Category([
            'name' => clean($names['en'])
        ]);

        foreach ($names as $key => $value) {
            $category->translations()->create([
                'lang' => $key,
                'name' => $value
            ]);
        }

        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);

        return response()->json($category);
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->all());

        return response()->json($category, 200);
    }

    public function destroy($id)
    {
        Category::destroy($id);

        return response()->json(null, 204);
    }
}
