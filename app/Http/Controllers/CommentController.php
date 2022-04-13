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
        return Comment::where('product_id', $productId)->where('is_verified', true)->get();
    }

    public function store(Request $request)
    {
        request()->validate([
            'comment' => 'required',
            'product_id' => 'required'
        ]);

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
        $products = auth()->user()->products;
        foreach ($products as $product) {
            $comments[] = $product->comments;
        };
        return collect($comments)->flatten(1)->toArray();
    }

    public function reply(Comment $comment, Request $request)
    {
        $request->validate([
            'reply' => 'required'
        ]);

        $comment->reply = $request->reply;
        $comment->save();

        return $comment;
    }
}
