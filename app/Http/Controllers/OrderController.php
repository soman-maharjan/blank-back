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
    public function getSellerOrder()
    {
        return SubOrder::where('user_id', auth()->user()->id)->get();
    }

    public function getUserOrder()
    {
        $order = new Order;
        return $order->userSubOrders();
    }

    public function subOrder(Order $order)
    {
        return $order->suborders;
    }
}
