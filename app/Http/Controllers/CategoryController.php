<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Category::all();
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required|max:255',
            'description' => 'nullable',
            'parent' => 'nullable',
            'attributes' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::create($request->only('title', 'description', 'parent', 'attributes'));

        return response([$category, 'message' => 'Category Created!'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $category;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required|max:255',
            'description' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response($validator->errors());
        }

        $category->update($request->only('title', 'description'));

        return response([$category, 'message' => 'Category Updated!'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response(['message' => 'Category Deleted!'], 201);
    }

    public function attribute($category)
    {
        if ($category == "null") {
            return null;
        }

        $category = Category::where('title', $category)->first();
        $attributes = $category->attributes;
        $arr = [];

        if ($attributes == null) {
            return null;
        }

        foreach ($attributes as $attr) {
            array_push($arr, $attr['value']);
        }

        return $arr;
    }

    public function product($category)
    {
        return Product::where('category', $category)->get();
    }
}
