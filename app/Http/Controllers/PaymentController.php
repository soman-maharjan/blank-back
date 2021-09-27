<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function handlePayment(Request $request)
    {
        //get 
        $serverProductIdentity = explode(",", $request->product_identity);

        $cartProductIdentity = (array) null;
        foreach ($request->cart['products'] as $product) {
            $cartProductIdentity[] = $product['_id'];
        }

        $order = new Order();
        $cartTotal = $order->getGrandTotal($request->cart);

        if (($serverProductIdentity == $cartProductIdentity) && ($cartTotal == $request->amount)) {
            $res = Http::withHeaders([
                'Authorization' => 'Key ' . env("KHALTI_SECRET_KEY")
            ])->post('https://khalti.com/api/v2/payment/verify/', [
                'amount' => $cartTotal,
                'token' => $request->token,
            ]);

            return $res;
        } else {
            return response()->json([
                'server' => $serverProductIdentity,
                'cart' => $cartProductIdentity
            ]);
        }
    }

    public function paymentSuccess()
    {
    }

    public function paymentCancel()
    {
    }
}
