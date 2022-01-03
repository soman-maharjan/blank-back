<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function store(Request $request)
    {
        if ($request->hasFile('images')) {
            foreach ($request->images as $image) {
                $filenameWithExt = $image->getClientOriginalName();

                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

                $extension = $image->getClientOriginalExtension();

                $filenameToStore = $filename . '_' . time() . '.' . $extension;

                $path = $image->storeAs('public/images', $filenameToStore);

                $img = new Image();
                $img->filename = $filenameToStore;
                $img->filenameWithExt = $filenameWithExt;
                $img->userFilename = $filename;
                $img->extension = $extension;
                $img->position = null;
                $img->active = false;
                $img->redirectLink = $request->redirectLink;
                $img->save();
            }

            return response("Image Uploaded!", 200);
        }
    }

    public function index()
    {
        return Image::all();
    }

    public function updateAd(Request $request)
    {
        if ($request->option == 'delete') {
            $image = Image::find($request->id);
            if (\File::exists(storage_path('app/public/images/' . $image->filename))) {
                \File::delete(storage_path('app/public/images/' . $image->filename));
            }
            $image->delete();
        } else {
            $product = Image::all()->where('active', '=', true)->where('position', '=', $request->option)->first();

            if (($product != "") && ($request->option != 'carousel')) {
                $product->active = false;
                $product->save();
            }

            Image::where('_id', $request->id)->update(array('active' => true, 'position' => $request->option));
        }
        return Image::all();
    }

    function activeAd()
    {
        return Image::where('active', true)->get();
    }
}
