<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressFormRequest;
use App\Models\Address;
use App\Models\SubOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    //validate the form using the rules in AddressFormRequest before passing it to the store function.
    public function store(AddressFormRequest $request)
    {
        $data = $request->all();
        //by default set the type of the address as pickup
        $data['type'] = 'pickup';
        Address::create($data);
        //change the status of the order to 'Ready To Ship'
        SubOrder::where('_id', $request->orderId)->update(array('status' => 'Ready To Ship'));
        //return the suborder
        return SubOrder::find($request->orderId);
    }

    public function show($id)
    {
        //return all the addresses where the orderId matches the requested order id and the type is pickup
        return Address::all()->where('orderId', $id)->where('type', 'pickup')->first();
    }

    public function validateAddress(Request $request)
    {
        //validate address before accepting the payment
        $validator = Validator::make($request->address, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'company' => ['nullable'],
            'address' => ['required'],
            'apartment' => ['nullable'],
            'zip_code' => ['nullable', 'numeric'],
            'city' => ['required'],
            'state' => ['required'],
            'house_number' => ['nullable'],
            'country' => ['required', 'string'],
            'phone_number' => ['integer', 'required'],
        ]);

        //return errors if the validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        return response()->json(['success' => 'Data Validated!'], 200);
    }
}
