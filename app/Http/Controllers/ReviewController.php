<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\SubOrder;

class ReviewController extends Controller
{
    public function index()
    {
        return Review::where('_id', auth()->user()->id)->get();
    }

    public function store()
    {
    }

    public function unreviewed()
    {
        $ord = [];
        $unreviewed = [];
        foreach (auth()->user()->orders as $order) {
            $ord = array_merge($ord, $order->suborders->toArray());
        }

        foreach ($ord as $o) {
            if ((new SubOrder($o))->review == '') {
                array_push($unreviewed, $o);
            }
        }

        return $unreviewed;
    }
}
