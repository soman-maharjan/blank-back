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

    public function product(Request $request)
    {
        //        return $request;
        $product = new Product();

        $arr = Category::where('parent', $request->value)->pluck('title');
        $arr[] = $request->value;

        return $product->whereIn('category', $arr)
            ->when($request->min != null, function ($q) {
                return $q->whereRaw([
                    "sku.price" => ['$gt' => (float) request('min')]
                ]);
            })
            ->when($request->max != null, function ($q) {
                return $q->whereRaw([
                    "sku.price" => ['$lt' => (float)request('max')]
                ]);
            })
            ->when($request->rating != "0", function ($q) {
                return $q->whereRaw([
                    "rating" => ['$gte' => (int) request('rating')]
                ]);
            })->when($request->sort != "relevance", function ($q) use ($request) {
                if ($request->sort == 'zToA') {
                    return $q->orderBy('productName', 'DESC');
                } else if ($request->sort == 'new') {
                    return $q->orderBy('created_at', 'DESC');
                } else if ($request->sort == 'aToZ') {
                    return $q->orderBy('productName', 'ASC');
                } else if ($request->sort == 'highToLow') {
                    return $q->orderBy('sku.price', 'DESC');
                } else if ($request->sort == 'lowToHigh') {
                    return $q->orderBy('sku.price', 'ASC');
                }
            })->paginate(20);
    }

    public function categoryProduct($category)
    {
        $arr = Category::where('parent', $category)->pluck('title');
        $arr[] = $category;
        return Product::whereIn('category', $arr)->where('is_verified', true)->take(15)->get();
        // return Product::whereIn('category', $arr)->take(15)->get();
    }
}
