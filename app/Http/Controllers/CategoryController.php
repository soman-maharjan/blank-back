<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function store(Request $request)
    {
        $data = $request->all();
        //validating the data before storing
        $validator = Validator::make($data, [
            'title' => 'required|max:255',
            'description' => 'nullable',
            'parent' => 'nullable',
            'attributes' => 'nullable'
        ]);

        //return errors if the validation of the data fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create a new category record in the database using the listed keys
        $category = Category::create($request->only('title', 'description', 'parent', 'attributes'));
        //return success message
        return response([$category, 'message' => 'Category Created!'], 201);
    }

    public function show(Category $category)
    {
        //using route model binding then returning the category data
        return $category;
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required|max:255',
            'description' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response($validator->errors());
        }

        //updating category 
        $category->update($request->only('title', 'description'));

        return response([$category, 'message' => 'Category Updated!'], 201);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response(['message' => 'Category Deleted!'], 201);
    }

    public function attribute($category)
    {
        //check if the data sent from frontend is empty
        if ($category == "null") {
            return null;
        }
        //select category from the database where title matches the one from the frontend
        $category = Category::where('title', $category)->first();
        //get all the attributes of the selected category
        $attributes = $category->attributes;
        $arr = [];

        //if the category is null then return null
        if ($attributes == null) {
            return null;
        }

        // create an array of attribute values as the attributes also contains id
        foreach ($attributes as $attr) {
            array_push($arr, $attr['value']);
        }

        // return the array of attrbute values
        return $arr;
    }

    public function product(Request $request)
    {
        $product = new Product();
        // get all categories where parent attribute matches the request value
        $arr = Category::where('parent', $request->value)->pluck('title');
        $arr[] = $request->value;

        //get product based on the following conditions
        return $product->whereIn('category', $arr)
            // if the request has min value
            ->when($request->min != null, function ($q) {
                //get products where the price is greater than the min value
                return $q->whereRaw([
                    "sku.price" => ['$gt' => (float) request('min')]
                ]);
            })
            // if the request has max value
            ->when($request->max != null, function ($q) {
                //get products where the price is less than the max value
                return $q->whereRaw([
                    "sku.price" => ['$lt' => (float)request('max')]
                ]);
            })
            // if the rating values is not 0
            ->when($request->rating != "0", function ($q) {
                //get products whose rating is greater than or equal to request rating value
                return $q->whereRaw([
                    "rating" => ['$gte' => (int) request('rating')]
                ]);
            })
            //if the sort value is not relevance
            ->when($request->sort != "relevance", function ($q) use ($request) {
                //order the product by descending order
                if ($request->sort == 'zToA') {
                    return $q->orderBy('productName', 'DESC');
                }
                //order the products by descending order of created_at attribute
                else if ($request->sort == 'new') {
                    return $q->orderBy('created_at', 'DESC');
                }
                // order the products by ascending order of product name
                else if ($request->sort == 'aToZ') {
                    return $q->orderBy('productName', 'ASC');
                }
                // order by price (hightest price first)
                else if ($request->sort == 'highToLow') {
                    return $q->orderBy('sku.price', 'DESC');
                }
                //order by price (lowest price first)
                else if ($request->sort == 'lowToHigh') {
                    return $q->orderBy('sku.price', 'ASC');
                }
            })->paginate(20);
    }

    public function categoryProduct($category)
    {
        $arr = Category::where('parent', $category)->pluck('title');
        $arr[] = $category;
        // get products where the category matches and is verified, only return 15 data.
        return Product::whereIn('category', $arr)->where('is_verified', true)->take(15)->get();
        // return Product::whereIn('category', $arr)->take(15)->get();
    }
}
