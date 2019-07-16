<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
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
        $names = $request->input('data');

        $category = Category::create();

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
        $category = $request->input('data');

        foreach ($category['translations'] as $translation) {
            Translation::find($translation['id'])->update(
                Arr::only($translation, ['name', 'description'])
            );
        }

        return response()->json(['success' => true], 200);
    }

    public function destroy($id)
    {
        Category::destroy($id);

        return response()->json(null, 204);
    }
}
