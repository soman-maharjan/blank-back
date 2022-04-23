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
            //if the sort value is not relevance
            ->when($request->sort != "relevance", function ($q) use ($request) {
                //order the product by descending order
                if ($request->sort == 'zToA') {
                    return $q->orderBy('productName', 'DESC');
                }
                //order the products by descending order of created_at attribute
                else if ($request->sort == 'new') {
                    return $q->orderBy('created_at', 'DESC');
                }
                // order the products by ascending order of product name
                else if ($request->sort == 'aToZ') {
                    return $q->orderBy('productName', 'ASC');
                }
                // order by price (hightest price first)
                else if ($request->sort == 'highToLow') {
                    return $q->orderBy('sku.price', 'DESC');
                }
                //order by price (lowest price first)
                else if ($request->sort == 'lowToHigh') {
                    return $q->orderBy('sku.price', 'ASC');
                }
            })
            ->paginate(20);
    }
}
