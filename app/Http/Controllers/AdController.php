<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function store(Request $request)
    {
        // check if the request contains file with the name as images 
        if ($request->hasFile('images')) {
            // if it contains file then loop through each one and save it in the system
            foreach ($request->images as $image) {
                //get the original name of the file
                $filenameWithExt = $image->getClientOriginalName();
                // take the file name only
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

                // taking the extension of the file and storing it in the extension variable 
                $extension = $image->getClientOriginalExtension();

                //creating a new variable with filename, time and extension so that it would be unqiue
                $filenameToStore = $filename . '_' . time() . '.' . $extension;

                //store it in the system with the new filename
                $path = $image->storeAs('public/images', $filenameToStore);

                //storing the file and other information
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
            // return a success response
            return response("Image Uploaded!", 200);
        }
    }

    public function index()
    {
        //return all data from the images table
        return Image::all();
    }

    public function updateAd(Request $request)
    {
        // check if the request option is delete
        if ($request->option == 'delete') {
            // if it is a delete option then get the file name from the database
            $image = Image::find($request->id);
            // if the file exists then delete it
            if (\File::exists(storage_path('app/public/images/' . $image->filename))) {
                \File::delete(storage_path('app/public/images/' . $image->filename));
            }
            // deleting the image data from the database after deleting the image
            $image->delete();
        } else {
            // if the request is not to delete then change the position of the image.
            $product = Image::all()->where('active', '=', true)->where('position', '=', $request->option)->first();
            
            //after changing the product position set the image as inactive
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
        //return all the ad with the property active as true.
        return Image::where('active', true)->get();
    }
}
