<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->all();

        $validator = Validator::make($data, [
            'productName' => 'required|max:255',
            'description' => 'nullable',
            'category' => 'required',
            'boxContents' => 'required',
            'color' => 'required',
            'variation' => 'required',
            'sku' => 'nullable',
            'attributes' => 'nullable',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // $data->push("user_id" => auth()->user()->_id);

        $data['user_id'] = auth()->user()->_id;
        $data['is_active'] = true;

        Product::create($data);

        return response(['message' => 'Product Created!'], 201);
    }

    public function image(Request $request)
    {
        $request->file('image')->storeAs('public/images', $request->file('image')->getClientOriginalName());
        return response()->json(['messsage' => "Image Uploaded"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'productName' => 'required|max:255',
            'description' => 'nullable',
            'category' => 'required',
            'boxContents' => 'required',
            'color' => 'required',
            'variation' => 'required',
            'sku' => 'required',
            'attributes' => 'nullable',
            'image' => 'required',
            'rating' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product->productName = $data['productName'];
        $product->description = $data['description'];
        $product->category = $data['category'];
        $product->boxContents = $data['boxContents'];
        $product->color = $data['color'];
        $product->variation = $data['variation'];
        $product->sku = $data['sku'];
        $product->attributes = $data['attributes'];
        $product->image = $data['image'];

        $product->save();

        return response(['message' => 'Product Updated!'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function userProduct()
    {
        $user = User::where('_id', auth()->user()->_id)->first();
        return $user->products;
    }
}
