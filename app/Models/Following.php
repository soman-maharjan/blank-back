<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

// use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    use HasFactory;

    protected $connection = "mongodb";
    protected $collection = "followings";

    protected $fillable = ['user_id', 'followings'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
