<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'addresses';

    protected $guarded = [];

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
