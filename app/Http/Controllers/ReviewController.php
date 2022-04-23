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
        //storing images in the system if it is uploaded by user
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

        $review->updateProductRating($request->product_id);

        return $this->unreviewed();
    }

    public function unreviewed()
    {
        $ord = [];
        $unreviewed = [];
        foreach (auth()->user()->orders as $order) {
            $ord = array_merge($ord, $order->suborders->toArray());
        }

        foreach ($ord as $o) {
            if (!Review::where('suborder_id', $o['_id'])->exists()) {
                array_push($unreviewed, $o);
            }
        }

        return $unreviewed;
    }

    public function destroy(Review $review)
    {
        // deleting all the images from the system if it exists
        foreach ($review->images as $img) {
            if (\File::exists(storage_path('app/public/images/' . $img))) {
                \File::delete(storage_path('app/public/images/' . $img));
            }
        }
        $review->delete();
        return response(['message' => 'Review Deleted!'], 201);
    }

    public function reviews(Product $product)
    {
        //get product reviews with user and suborder they belong to
        return $product->reviews->each(function ($rev) {
            $rev->user;
            $rev->suborder;
        });
    }

    public function sellerReviews(){
        $reviews = [];
        foreach(auth()->user()->products as $product){
            $reviews[] = $product->reviews;
        }

        return collect($reviews)->flatten(1);
        return Review::all();
    }
}
