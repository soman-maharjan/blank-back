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

        if ($request->type == 'KHALTI') {

            $data = [
                'user_id'     => auth()->user()->id,
                'mobile'     => $request->mobile,
                'amount'     => ($request->amount / 100.00),
                'pre_token' => $request->token,
                'status' => 0,
                'order_id' => null,
                'verified_token' => null,
                'type' => null,
                'provider' => $request->type,
                'fee' => 3
            ];

            try {

                $payment = $this->payment->create($data);

                $serverProductIdentity = explode(",", $request->product_identity);

                $cartProductIdentity = (array) null;
                foreach ($request->cart['products'] as $product) {
                    $cartProductIdentity[] = $product['_id'];
                }

                $cartTotal = $order->getGrandTotal($request->cart);

                if (($serverProductIdentity == $cartProductIdentity) && ($cartTotal == $request->amount)) {
                    if ($this->verifyKhaltiPayment($cartTotal, $request->token, $payment)) {
                        $data = $order->storeOrder($request->cart, $request->address);
                        $data['type'] = $payment['type'];
                        $data['name'] = $payment['name'];

                        $payment->update(['order_id' => $data['orderId']]);

                        return response()->json($data, 200);
                    }
                }
            } catch (Exception $e) {
                return response()->json(['error' => 'Something went Wrong , Try Again !!'], 422);
            }
            return response()->json(['error' => 'Something went Wrong , Try Again !!'], 422);
        }
    }

    public function verifyKhaltiPayment($cartTotal, $token, $payment)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Key ' . env("KHALTI_SECRET_KEY")
        ])->post('https://khalti.com/api/v2/payment/verify/', [
            'amount' => $cartTotal,
            'token' => $token,
        ]);

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
