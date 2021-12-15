<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

// use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $connection = "mongodb";
    protected $collection = "reviews";

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function suborder()
    {
        return $this->belongsTo(SubOrder::class, 'suborder_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updateProductRating($id)
    {
        $product = Product::findOrFail($id);
        $newRating = Review::where('product_id', $id)->pluck('rating')->avg();
        $product->update(['rating' => $newRating]);
    }
}
