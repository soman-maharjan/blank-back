<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function index()
    {
        return User::with('roles')->get()->each(function ($user) {
            unset($user['role_ids']);
            $user->role = $user->roles->pluck('name');
            unset($user['roles']);
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    public function username(User $user)
    {
        return $user->name;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
//        $request->validate([
//            'name' => 'required',
//            'bio' => 'nullable',
//            'email' => 'required|email|unique:users'
//        ]);

        $u = User::where('_id', $request->_id)->first();
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
        $user->email = $request->email;
//        return $request;
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
            return response()->json(['message' => 'Updated!', 'data' => $user], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'User Deleted!'], 200);
    }
}
