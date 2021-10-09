<?php

namespace App\Http\Controllers;

use App\Models\Following;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    public function follow(Request $request)
    {
        $result = Following::where('user_id', auth()->user()->id)->first();
        $followings = $result->followings;

        if (!(in_array($request->id, $followings))) {
            array_push($followings, $request->id);
            $result->update(['followings' => $followings]);
        }

        return response()->json(['message' => 'User Followed!']);
    }

    public function unfollow(Request $request)
    {
        $result = Following::where('user_id', auth()->user()->id)->first();
        $followings = $result->followings;

        if (($key = array_search($request->id, $followings)) !== false) {
            unset($followings[$key]);
            $result->update(['followings' => $followings]);
        }

        return response()->json(['message' => 'User UnFollowed!']);
    }
}
