<?php

namespace App\Models;

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
}
