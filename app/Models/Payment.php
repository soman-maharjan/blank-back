<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

// use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $collection = "payments";
    protected $connection = "mongodb";

    protected $fillable = [
        'user_id', 'mobile', 'amount', 'pre_token', 'status', 'verified_token', 'order_id', 'type', 'name', 'provider', 'fee'
    ];
}
