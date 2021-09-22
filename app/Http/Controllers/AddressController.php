<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressFormRequest;
use App\Models\Address;
use App\Models\SubOrder;

class AddressController extends Controller
{
    public function store(AddressFormRequest $request)
    {
        $data = $request->all();
        $data['type'] = 'pickup';
        Address::create($data);

        SubOrder::where('_id', $request->orderId)->update(array('status' => 'Ready To Ship'));

        return SubOrder::find($request->orderId);
    }

    public function show($id)
    {
        return Address::all()->where('orderId', $id)->where('type', 'pickup')->first();
    }
}
