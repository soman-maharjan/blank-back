<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        request()->validate([
            'comment' => 'required',
            'productId' => 'required'
        ]);

        Comment::create([
            'user_id' => auth()->user()->id,
            'comment' => $request->comment,
            'productId' => $request->productId
        ]);

        return response('Comment Posted!', 204);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
