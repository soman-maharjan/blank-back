<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\SubOrder;
use Carbon\Carbon;
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

            $order = new Order();
            $order->address_id = $address->_id;
            $order->grandTotal = $grandTotal;
            $order->user_id = auth()->user()->id;
            $order->save();


            foreach ($cart['products'] as $product) {
                $product['order_id'] = $order->_id;
                $product['status'] = "pending";
                $product['created_at'] = Carbon::now();
                $product['updated_at'] = Carbon::now();
                SubOrder::create($product);
            }

            return response()->json(
                [
                    'products' => $cart['products'],
                    'address' => $address,
                    'grandTotal' => $grandTotal,
                    'orderId' => $order->_id
                ],
                200
            );
        }
        return response()->json('Error!', 422);
    }

    public function getOrder()
    {
        return SubOrder::where('user_id', auth()->user()->id)->get();
    }
}
