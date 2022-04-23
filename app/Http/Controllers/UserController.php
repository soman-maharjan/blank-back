<?php

namespace App\Http\Controllers;

use App\Mail\EmailUpdated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function index()
    {
        // get user with their roles
        return User::with('roles')->get()->each(function ($user) {
            unset($user['role_ids']);
            $user->role = $user->roles->pluck('name');
            unset($user['roles']);
        });
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(User $user)
    {
        return $user;
    }

    public function username(User $user)
    {
        return $user->name;
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        $u = User::where('_id', $request->_id)->first();
        //validate user data except their own email
        $validator = \Validator::make($request->all(), [
            'name' => ['required'],
            'bio' => ['nullable'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($u),
            ],
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 422);
        }

        $user->name = $request->name;
        $user->bio = $request->bio;
        $oldEmail = $user->email;
        $user->email = $request->email;
        foreach ($request->roles as $roles) {
            if ($roles[array_keys($roles)[0]] == true) {
                $user->assignRole(array_keys($roles)[0]);
            } else {
                $user->removeRole(array_keys($roles)[0]);
            }
        }
        if ($user->save()) {
            unset($user['role_ids']);
            $user->role = $user->roles->pluck('name');
            unset($user['roles']);
            if ($oldEmail != $request->email) {
                $data = [
                    'title' => 'Email Updated',
                    'email' => $request->email
                ];
                // if the email was changed then send an 'email changed' mail to the user.
                Mail::to($user->email)->send(new EmailUpdated($data));
            }

            return response()->json(['message' => 'Updated!', 'data' => $user], 200);
        }
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'User Deleted!'], 200);
    }
}
