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
        return $this->hasOne(SubOrder::class);
    }
}
