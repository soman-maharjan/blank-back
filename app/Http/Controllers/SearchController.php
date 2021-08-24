<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search($value)
    {
        $product = new Product();
        return $product->search($value);
    }
}
