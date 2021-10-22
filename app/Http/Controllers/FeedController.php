<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FeedController extends Controller
{
    public function feed()
    {
        $followings = auth()->user()->followings->followings;

        $merged = new Collection();

        if ($followings != []) {
            foreach ($followings as $following) {
                $user = User::where('_id', $following)->first();
                $merged = $merged->merge($user->products);
            }

            return $merged->sortByDesc('created_at')->values();
        }

        return response()->json(["message" => "No Products"], 422);


        // $product =  DB::table('products')->get();
        // return $product->sortByDesc('created_at')->values();
    }
}
