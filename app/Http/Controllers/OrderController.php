<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $price = 0;
        $grandTotal = 0;
        $cart = $request->cart;
        $address = $request->address;



        //validating shipping address
        $validator = Validator::make($address, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'company' => ['nullable'],
            'address' => ['required'],
            'apartment' => ['nullable'],
            'zip_code' => ['nullable', 'numeric'],
            'city' => ['required'],
            'state' => ['required'],
            'house_number' => ['nullable'],
            'country' => ['required', 'string'],
            'phone_number' => ['required'],
        ]);

        //return errors if the validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //calculating the total price of products in backend using product sku and quantity from frontend
        foreach ($cart['products'] as $product) {
            $prod = Product::where('_id', $product['_id'])->first();
            foreach ($prod->sku as $sku) {
                if ($sku['sellerSku'] == $product['sku']['sellerSku']) {
                    $price = $sku['price'];
                    $grandTotal += ($price * $product['quantity']);
                }
            }
        }

        //if the grand total calulated in the backend matches with the total from frontend then enter the
        //order in the database
        //else return an error
        if ($grandTotal == $cart['total']) {
            $address = Address::create($address);
            $cart['address_id'] = $address->_id;
            $cart['user_id'] = auth()->user()->id;
            Order::create($cart);
            return response()->json('Order Added!', 200);
            
        }

        return response()->json([$message = 'Error!'], 422);
    }

    public function show()
    {
        
    }
}
