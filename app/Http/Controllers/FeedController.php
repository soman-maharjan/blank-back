<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function feed()
    {
        $followings = User::where('_id', "61629ae42c3800001a003c73")->first()->followings->followings;
        if ($followings != []) {
            foreach ($followings as $following) {
                $user = User::where('_id', $following)->first();
                return collect($user->products)->sortBy('rating');
            }
        }
    }
}
