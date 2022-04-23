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
        // data for admin dashboard
        //count the number of users in the database and store it in the userCOunt variable
        $userCount = User::all()->count();

        //count number of products
        $productCount = Product::all()->count();

        // get the number of products that was added in the last month
        $productLastMonthCount = Product::where(
            'created_at',
            '>=',
            Carbon::now()->subMonth()
        )->count();

        // total number of user added last month
        $userLastMonthCount = User::where(
            'created_at',
            '>=',
            Carbon::now()->subMonth()
        )->count();

        // total sum of all the transactions
        $totalTransaction = Payment::sum('amount');

        //total transaction in the last month
        $totalLastMonthTransaction = Payment::where(
            'created_at',
            '>=',
            Carbon::now()->subMonth()
        )->sum('amount');

        //create an array of all the statistic
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
        // total number of product order
        $totalOrders = SubOrder::where('user_id', auth()->user()->id)->count();

        // total product orders in the last month
        $totalOrdersLastMonth = SubOrder::where('user_id', auth()->user()->id)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->count();

        // total amount spent
        $totalSales = SubOrder::where('user_id', auth()->user()->id)
            ->sum('totalPrice');

        // sum of total price of product that was sold last month
        $totalSalesLastMonth = SubOrder::where('user_id', auth()->user()->id)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->sum('totalPrice');

        // total product added last month
        $productLastMonthCount = Product::where('user_id', auth()->user()->id)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->count();

        // number of product added by seller
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
        // get all the product order of an particular user
        $subOrders = $order->userSubOrders();
        //count the number of orders
        $orderCount = count($subOrders);

        $pendingOrderCount = 0;
        // total amount spent by a customer
        $totalAmountSpent = Payment::where('user_id', auth()->user()->id)
            ->sum('amount');

        // count the number of product orders that are pending
        foreach ($subOrders as $ord) {
            if ($ord['status'] == 'pending') {
                $pendingOrderCount++;
            }
        }

        //create an array of all the details
        $data = [
            'orderCount' => $orderCount,
            'pendingOrderCount' => $pendingOrderCount,
            'totalAmountSpent' => $totalAmountSpent
        ];

        return $data;
    }
}
