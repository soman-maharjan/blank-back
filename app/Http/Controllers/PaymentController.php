<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function handlePayment(Request $request)
    {
        $order = new Order();

        // check the payment gateway
        if ($request->type == 'KHALTI') {
            //create an array of payment data
            $data = [
                'user_id' => auth()->user()->id,
                'mobile' => $request->mobile,
                'amount' => ($request->amount / 100.00),
                'pre_token' => $request->token,
                'status' => 0,
                'order_id' => null,
                'verified_token' => null,
                'type' => null,
                'provider' => $request->type,
                'fee' => 3
            ];

            try {
                //create the date in payments table
                $payment = $this->payment->create($data);
                //ids of product in the order
                $serverProductIdentity = explode(",", $request->product_identity);

                $cartProductIdentity = (array)null;
                //ids of product in the cart in the frontend
                foreach ($request->cart['products'] as $product) {
                    $cartProductIdentity[] = $product['_id'];
                }

                //get the grant total of the products in the cart
                $cartTotal = $order->getGrandTotal($request->cart);

                //check if the ids in the server is same as the ids in the cart and if the total matches
                if (($serverProductIdentity == $cartProductIdentity) && ($cartTotal == $request->amount)) {
                    //verifying the payment through the payment gateway server
                    if ($this->verifyKhaltiPayment($cartTotal, $request->token, $payment)) {
                        // after verification, store the order in the orders table
                        $data = $order->storeOrder($request->cart, $request->address);
                        $data['type'] = $payment['type'];
                        $data['name'] = $payment['name'];
                        //updete order id in the payments table.
                        $payment->update(['order_id' => $data['orderId']]);

                        return response()->json($data, 200);
                    }
                }
            } 
            //return errors
            catch (Exception $e) {
                return response()->json(['error' => 'Something went Wrong , Try Again !!'], 422);
            }
            return response()->json(['error' => 'Something went Wrong , Try Again !!'], 422);
        }
    }

    public function verifyKhaltiPayment($cartTotal, $token, $payment)
    {
        // sending validaiton request to khalti server with cart total and token
        $response = Http::withHeaders([
            'Authorization' => 'Key ' . env("KHALTI_SECRET_KEY")
        ])->post('https://khalti.com/api/v2/payment/verify/', [
            'amount' => $cartTotal,
            'token' => $token,
        ]);

        // if the payment is verified by payment gateway server, store the token, type and name in the payments table.
        if ($response->successful() && isset($response['idx'])) {
            $payment->update(['status' => 1, 'verified_token' => $response['idx'], 'type' => $response['type']['name'], 'name' => $response['user']['name']]);
            return true;
        }

        return false;
    }

    public function index()
    {
        return Payment::all();
    }
}
