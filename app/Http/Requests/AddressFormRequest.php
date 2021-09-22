<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
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
            'pickup_date' => ['required', 'date', 'after:tomorrow'],
            'note' => ['nullable']
        ];
    }
}
