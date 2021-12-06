<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class SubOrder extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'suborders';

    protected $guarded = [];

    public function orders()
    {
        $this->belongsTo(Order::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'subOrder_id', '_id');
    }
}
