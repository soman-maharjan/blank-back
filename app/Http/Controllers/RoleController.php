<?php

namespace App\Http\Controllers;

use Maklad\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return Role::all()->pluck('name');
    }
}
