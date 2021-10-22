<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\SubOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // public function placeOrder(Request $request)
    // {
    //     $grandTotal = 0;
    //     $cart = $request->cart;
    //     $address = $request->address;

    //     //validating shipping address
    //     $validator = Validator::make($address, [
    //         'first_name' => ['required', 'string', 'max:255'],
    //         'last_name' => ['required', 'string', 'max:255'],
    //         'company' => ['nullable'],
    //         'address' => ['required'],
    //         'apartment' => ['nullable'],
    //         'zip_code' => ['nullable', 'numeric'],
    //         'city' => ['required'],
    //         'state' => ['required'],
    //         'house_number' => ['nullable'],
    //         'country' => ['required', 'string'],
    //         'phone_number' => ['integer', 'required'],
    //     ]);

    //     //return errors if the validation fails
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     $order = new Order();
    //     $grandTotal = $order->getGrandTotal($cart);

    //     //if the grand total calulated in the backend matches with the total from frontend then enter the
    //     //order in the database
    //     //else return an error
    //     if ($grandTotal == $cart['total']) {
    //         $address['type'] = "delivery";
    //         $address = Address::create($address);

    //         $order->address_id = $address->_id;
    //         $order->grandTotal = $grandTotal;
    //         $order->user_id = auth()->user()->id;
    //         $order->save();

    //         foreach ($cart['products'] as $product) {
    //             $product['order_id'] = $order->_id;
    //             $product['status'] = "pending";
    //             $product['created_at'] = Carbon::now();
    //             $product['updated_at'] = Carbon::now();
    //             SubOrder::create($product);
    //         }

    //         return response()->json(
    //             [
    //                 'products' => $cart['products'],
    //                 'address' => $address,
    //                 'grandTotal' => $grandTotal,
    //                 'orderId' => $order->_id
    //             ],
    //             200
    //         );
    //     }
    //     return response()->json('Error!', 422);
    // }

    public function getSellerOrder()
    {
        return SubOrder::where('user_id', auth()->user()->id)->get();
    }

    public function getUserOrder()
    {
        $allOrders = [];

        $orders = Order::where('user_id', auth()->user()->id)->get();
        foreach ($orders as $order) {
            $allOrders[] = $order->suborders;
        }

        return collect($allOrders)->flatten(1)->toArray();
    }

    public function subOrder(Order $order)
    {
        return $order->suborders;
    }
}
