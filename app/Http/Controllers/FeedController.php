<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FeedController extends Controller
{
    public function feed()
    {
        // get the followings of a user
        $followings = auth()->user()->followings->followings;
        $merged = new Collection();
        // if the followings of an user is not empty
        if ($followings != []) {
            foreach ($followings as $following) {
                // select user data from using the ids from the following list of the user
                $user = User::where('_id', $following)->first();
                //merge active and verified products to $merged variable. 
                $merged = $merged->merge($user->activeProducts($user));
            }
            // sort products based on created date (newest one first)
            return $merged->sortByDesc('created_at')->values();
        }
        return response()->json(["message" => "No Products"], 422);
    }
}
