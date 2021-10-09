<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

// use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;

    protected $connection = "mongodb";
    protected $collection = "followers";

    protected $fillable = ['user_id', 'followers'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
