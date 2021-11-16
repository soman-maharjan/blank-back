<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/(?=^.{8,}$)(?=.*\d)(?=.*[!@#$%^&*]+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/'],
            // 'phone_number' => ['required', 'integer', 'unique:users'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            // 'phone_number' => $request['phone_number'],
            'bio' => null,
            'profileImage' => "default.jpg"
        ]);

        return response(['success' => true], 200);
    }

    public function changePassword(Request $request)
    {
        // return Auth::guard()->attempt(auth()->user()->email, $request->current_password);

        $user = User::find(auth()->user()->_id);

        if (Hash::check($request['current_password'], $user->password) && ($request->new_password == $request->confirm_password)) {
            if (!Hash::check($request['new_password'], $user->password)) {
                $user->update(['password' => Hash::make($request->new_password)]);
                return response()->json(["success" => "Password Changed!"], 200);
            }
            return response()->json(["error" => "Cannot use old Password!"], 422);
        } else {
            return response()->json(["error" => "Incorrect Password!"], 422);
        }
    }
}
