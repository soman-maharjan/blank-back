<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        return Comment::all();
    }

    public function productComments($productId)
    {
        // select comments where product id matches productId and is verified by the admin
        return Comment::where('product_id', $productId)->where('is_verified', true)->get();
    }

    public function store(Request $request)
    {
        //validate comment
        request()->validate([
            'comment' => 'required',
            'product_id' => 'required'
        ]);

        //create a new comment record 
        Comment::create([
            'user_id' => auth()->user()->id,
            'comment' => $request->comment,
            'product_id' => $request->product_id,
            'is_verified' => false,
            'reply' => null
        ]);

        return response('Comment Posted!', 204);
    }

    public function show(Comment $comment)
    {
        return $comment;
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response('Comment Deleted!', 204);
    }

    public function verify(Comment $comment)
    {
        $comment->is_verified = !$comment->is_verified;
        $comment->save();
        return $comment;
    }

    public function sellerComments()
    {
        $comments = [];
        //get user's products
        $products = auth()->user()->products;
        //for each products of the user, get the comments
        foreach ($products as $product) {
            $comments[] = $product->comments;
        };
        // flatten the array of array to the depth of 1 and convert to array
        return collect($comments)->flatten(1)->toArray();
    }

    public function reply(Comment $comment, Request $request)
    {
        // check if the request has reply
        $request->validate([
            'reply' => 'required'
        ]);
        
        //update the reply field 
        $comment->reply = $request->reply;
        $comment->save();

        return $comment;
    }
}
