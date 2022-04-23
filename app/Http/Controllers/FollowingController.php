<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Following;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    public function follow(Request $request)
    {
        //Add user to following table
        $result = Following::where('user_id', auth()->user()->id)->first();
        $followings = $result->followings;
        // if the user is not in the following list then update the list with new user id
        if (!(in_array($request->id, $followings))) {
            array_push($followings, $request->id);
            $result->update(['followings' => $followings]);
        }

        //add user to followers table
        $result2 = Follower::where('user_id', $request->id)->first();
        $followers = $result2->followers;

        // if the user is not in the followers list then update the list with new user id
        if (!(in_array($request->id, $followers))) {
            array_push($followers, auth()->user()->id);
            $result2->update(['followers' => $followers]);
        }

        return response()->json(['message' => 'User Followed!']);
    }

    public function unfollow(Request $request)
    {
        $result = Following::where('user_id', auth()->user()->id)->first();
        $followings = $result->followings;

        // if the user id is in the followings list then remove it
        if (($key = array_search($request->id, $followings)) !== false) {
            unset($followings[$key]);
            $result->update(['followings' => $followings]);
        }

        //remove user from followers table
        $result2 = Follower::where('user_id', $request->id)->first();
        $followers = $result2->followers;
        // if the user id is in the followers list then remove it
        if (($key = array_search(auth()->user()->id, $followers)) !== false) {
            unset($followers[$key]);
            $result2->update(['followers' => $followers]);
        }

        return response()->json(['message' => 'User UnFollowed!']);
    }
}
