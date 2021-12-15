<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $guarded = [];

    public function suborders()
    {
        return $this->hasMany(SubOrder::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getGrandTotal($cart)
    {
        $grandTotal = 0;
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
        return $grandTotal;
    }

    public function storeOrder($cart, $address)
    {
        $order = new Order();
        $grandTotal = $order->getGrandTotal($cart);

        //if the grand total calulated in the backend matches with the total from frontend then enter the
        //order in the database
        //else return an error
        if ($grandTotal == $cart['total']) {
            $address['type'] = "delivery";
            $address = Address::create($address);

            $order->address_id = $address->_id;
            $order->grandTotal = $grandTotal;
            $order->user_id = auth()->user()->id;
            $order->save();

            foreach ($cart['products'] as $product) {
                $product['order_id'] = $order->_id;
                $product['status'] = "pending";
                $product['product_id'] = $product['_id'];
                $product['created_at'] = Carbon::now();
                $product['updated_at'] = Carbon::now();
                unset($product['is_active'],$product['is_verified'], $product['rating']);
                SubOrder::create($product);
            }

            $data =
                [
                    'products' => $cart['products'],
                    'address' => $address,
                    'grandTotal' => $grandTotal,
                    'orderId' => $order->_id
                ];

            return $data;
        }
    }

    public function userSubOrders()
    {
        $subOrders = [];

        $orders = Order::where('user_id', auth()->user()->id)->get();
        foreach ($orders as $order) {
            $subOrders[] = $order->suborders;
        }

        return collect($subOrders)->flatten(1)->toArray();
    }
}
