<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'products';

    // protected $fillable = ['productName', 'description', 'category', 'boxContents', 'color', 'sku', 'attributes', 'images'];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userProducts()
    {
        $user = User::where('_id', auth()->user()->_id)->first();
        return $user->products;
    }
}
