<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    // public function search($value)
    // {
    //     // $fuzzySearch = implode("%", str_split($word)); // e.g. test -> t%e%s%t
    //     // $fuzzySearch = "%$fuzzySearch%";

    //     Product::filter()->get();
    // }

    public function filter(Request $request)
    {
        $product = new Product();

        $fuzzySearch = implode("%", str_split($request->value)); // e.g. test -> t%e%s%t
        $fuzzySearch = "%$fuzzySearch%";

        return $product->where('productName', 'like', $fuzzySearch)
            ->when($request->min != "", function ($q) {
                return $q->whereRaw([
                    "sku.price" => ['$gt' => (float) request('min')]
                ]);
            })
            ->when($request->max != "", function ($q) {
                return $q->whereRaw([
                    "sku.price" => ['$lt' => (float)request('max')]
                ]);
            })
            ->when($request->rating != "", function ($q) {
                return $q->whereRaw([
                    "rating" => ['$gte' => (int) request('rating')]
                ]);
            })->get();

        // if ($request->min) {
        //     $product = $product->whereRaw([
        //         "sku.price" => ['$gt' => (float)2000]
        //     ]);
        // }

        // if ($request->max) {
        //     $product = $product->whereRaw([
        //         "sku.price" => ['$lt' => (float)3000]
        //     ]);
        // }

        // return $product->get();
    }
}
