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
                    "sku.price" => ['$gt' => (double)request('min')]
                ]);
            })
            ->when($request->max != "", function ($q) {
                return $q->whereRaw([
                    "sku.price" => ['$lt' => (double)request('max')]
                ]);
            })
            ->when($request->rating != "0", function ($q) {
                return $q->whereRaw([
                    "rating" => ['$gte' => (int)request('rating')]
                ]);
            })
            ->when($request->sort != "relevance", function ($q) use ($request) {
                if ($request->sort == 'zToA') {
                    return $q->orderBy('productName', 'DESC');
                } else if ($request->sort == 'new') {
                    return $q->orderBy('created_at', 'DESC');
                } else if ($request->sort == 'aToZ') {
                    return $q->orderBy('productName', 'ASC');
                } else if ($request->sort == 'highToLow'){
                    return $q->orderBy('sku.price', 'DESC');
                } else if ($request->sort == 'lowToHigh'){
                    return $q->orderBy('sku.price', 'ASC');
                }
            })
            ->paginate(20);
    }
}
