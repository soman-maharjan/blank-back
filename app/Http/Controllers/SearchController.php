<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function filter(Request $request)
    {
        $product = new Product();

        $fuzzySearch = implode("%", str_split($request->value));
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
            ->when($request->rating != "0", function ($q) {
                return $q->whereRaw([
                    "rating" => ['$gte' => (int) request('rating')]
                ]);
            })
            ->paginate(20);
    }
}
