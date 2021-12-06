<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return Review::where('user_id', auth()->user()->id)->get();
    }

    public function store(Request $request): array
    {
        $request->validate([
            'review' => 'required'
        ]);

        $var = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $var[] = $image->getClientOriginalName();
                $image->storeAs('public/images', $image->getClientOriginalName());
            }
        }

        $review = new Review();
        $review->review = $request->review;
        $review->images = $var;
        $review->suborder_id = $request->subOrderId;
        $review->user_id = auth()->user()->id;
        $review->rating = $request->rating;
        $review->product_id = $request->product_id;
        $review->save();

        return $this->unreviewed();
    }

    public function unreviewed()
    {
        // $ord = SubOrder::where('_id','617cd747b52f0000000053d5')->first();
        // return $ord->review;

        $ord = [];
        $unreviewed = [];
        foreach (auth()->user()->orders as $order) {
            $ord = array_merge($ord, $order->suborders->toArray());
        }

        foreach ($ord as $o) {
            if (!Review::where('suborder_id', $o['_id'])->exists()) {
                array_push($unreviewed, $o);
            }
            // return Review::where('subOrder_id', $o['_id'])->first();
            // if (((new SubOrder((array) $o))->review == "")) {
            //     array_push($unreviewed, $o);
            // }
        }

        return $unreviewed;
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return response(['message' => 'Review Deleted!'], 201);
    }

    /**
     * @param Product $product
     * @return mixed
     * returns reviews of product with user and suborder
     */
    public function reviews(Product $product)
    {
        return $product->reviews->each(function ($rev) {
            $rev->user;
            $rev->suborder;
        });
    }
}
