<?php

namespace App\Http\Controllers;

use App\Events\NewProduct;
use App\Models\Product;
use App\Models\SubOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        return Product::where('is_active', true)->get();
    }

    public function allProducts()
    {
        return Product::all();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->all();

        foreach ($data['sku'] as $key => $sku) {
            $data['sku'][$key]['price'] = (double)$data['sku'][$key]['price'];
            $data['sku'][$key]['quantity'] = (int)$data['sku'][$key]['quantity'];
        }

        $validator = Validator::make($data, [
            'productName' => 'required|max:255',
            'description' => 'nullable',
            'category' => 'required',
            'boxContents' => 'required',
            'color' => 'required',
            'variation' => 'required',
            'sku' => 'required',
            'attributes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data['user_id'] = auth()->user()->_id;
        $data['is_active'] = true;


        $product = new Product();
        $product->productName = $data['productName'];
        $product->description = $data['description'];
        $product->category = $data['category'];
        $product->color = $data['color'];
        $product->boxContents = $data['boxContents'];
        $product->variation = $data['variation'];
        $product->attributes = $data['attributes'];
        $product->sku = $data['sku'];
        $product->user_id = $data['user_id'];
        $product->is_active = $data['is_active'];
        $product->rating = 0;
        $product->is_verified = false;
        $product->save();

        foreach ($data['sku'] as $sku) {
            if ($request->hasFile($sku['sellerSku'])) {
                foreach ($request->file($sku['sellerSku']) as $file) {
                    $file->storeAs('public/images', $file->getClientOriginalName());
                }
            }
        }

        return response(['message' => 'Product Created!'], 201);
    }

    public function show(Product $product)
    {
//        return $product;
//        return $product = $product->load(['user' => function($query){
////            $query->unset('email');
//        }]);
        $p = $product->load(['user']);
        $username = $p->user->name;
        $p->unset('user');
        $p['username'] = $username;
        return $p;
    }

    public function edit(Product $product)
    {
        //
    }

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
        $product->is_verified = false;

        $product->save();

        return response(['message' => 'Product Updated!'], 201);
    }

    public function destroy(Product $product)
    {
        $images = [];
        foreach ($product->sku as $sku) {
            $images = array_merge($images, $sku['images']);
        }

        foreach ($images as $img) {
            if (\File::exists(storage_path('app/public/images/' . $img))) {
                \File::delete(storage_path('app/public/images/' . $img));
            }
        }

        $product->delete();
        return response()->json(['message' => 'Product Deleted!'], 200);
    }

    public function userProduct()
    {
        $product = new Product();
        return $product->userProducts();
    }

    public function changeStatus(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();
        $p = new Product();
        return $p->userProducts();
    }

    public function verify(Product $product)
    {
        $product->is_verified = !$product->is_verified;
        $product->save();
        if ($product->is_verified && $product->is_active) {
            broadcast(new NewProduct($product));
        }
        return $product;
    }

    public function topSelling()
    {
        $ids = SubOrder::raw(function ($collection) {
            return $collection->aggregate([
                ['$group' => [
                    '_id' => '$product_id',
                    'count' => ['$sum' => 1]
                ]],
                ['$sort' => [
                    'count' => -1
                ]]
            ]);
        })->take(15);


        foreach ($ids as $id) {
            $p = Product::where('_id', $id->_id)
                ->where('is_active', true)
                ->where('is_verified', true)
                ->first();
            if($p != null){
                $products[] = $p;
            }
        }

        return $products;

//        return \DB::table('suborders')
//            ->selectRaw('productName')
//            ->selectRaw('count(suborders)')
//            ->groupBy('product_id')
//            ->get();
//        return SubOrder::all()
//            ->groupBy('product_id')
//            ->take(15);
    }

    public function similar(Product $product)
    {
        foreach ($product->sku as $sku) {
            $prices[] = $sku['price'];
        }
        if (sizeof($prices) == 1) {
            $prices[] = 0;
        }

        $min = min($prices);
        $max = max($prices);

        return Product::where('productName', 'like', '%' . implode('%', str_split($product->productName)) . '%')
            ->orWhere('category', 'like', $product->category)
            ->orWhere('color', 'like', $product->color)
            ->orWhere('boxContents', 'like', $product->boxContents)
            ->orWhere(function ($query) use ($min, $max) {
                $query->orWhereRaw([
                    "sku.price" => [['$gt' => $min], ['$lt' => $max]]
                ]);
            })
            ->take(15)->get();
    }
}
