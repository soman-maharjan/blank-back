<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SubOrder;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function adminData()
    {
        $userCount = User::all()->count();

        $productCount = Product::all()->count();

        $productLastMonthCount = Product::where(
            'created_at', '>=', Carbon::now()->subMonth()
        )->count();

        $userLastMonthCount = Product::where(
            'created_at', '>=', Carbon::now()->subMonth()
        )->count();

        $totalTransaction = Payment::sum('amount');

        $totalLastMonthTransaction = Payment::where(
            'created_at', '>=', Carbon::now()->subMonth()
        )->sum('amount');

        $data = [
            'userCount' => $userCount,
            'productCount' => $productCount,
            'productLastMonthCount' => $productLastMonthCount,
            'userLastMonthCount' => $userLastMonthCount,
            'totalTransaction' => $totalTransaction,
            'totalLastMonthTransaction' => $totalLastMonthTransaction
        ];

        return $data;
    }

    public function sellerData()
    {
        $totalOrders = SubOrder::where('user_id', auth()->user()->id)->count();

        $totalOrdersLastMonth = SubOrder::where('user_id', auth()->user()->id)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->count();

        $totalSales = SubOrder::where('user_id', auth()->user()->id)
            ->sum('totalPrice');

        $totalSalesLastMonth = SubOrder::where('user_id', auth()->user()->id)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->sum('totalPrice');

        $productLastMonthCount = Product::where('user_id', auth()->user()->id)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->count();

        $productCount = Product::where('user_id', auth()->user()->id)
            ->count();

        $data = [
            'totalOrders' => $totalOrders,
            'totalOrdersLastMonth' => $totalOrdersLastMonth,
            'productLastMonthCount' => $productLastMonthCount,
            'productCount' => $productCount,
            'totalSales' => $totalSales,
            'totalSalesLastMonth' => $totalSalesLastMonth
        ];

        return $data;
    }

    public function customerData()
    {
        $order = new Order();
        $subOrders = $order->userSubOrders();
        $orderCount = count($subOrders);

        $pendingOrderCount = 0;
        $totalAmountSpent = Payment::where('user_id', auth()->user()->id)
            ->sum('amount');

        foreach ($subOrders as $ord) {
            if ($ord['status'] == 'pending') {
                $pendingOrderCount++;
            }
        }

        $data = [
            'orderCount' => $orderCount,
            'pendingOrderCount' => $pendingOrderCount,
            'totalAmountSpent' => $totalAmountSpent
        ];

        return $data;
    }
}
