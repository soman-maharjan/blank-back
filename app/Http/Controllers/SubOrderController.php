<?php

namespace App\Http\Controllers;

use App\Models\SubOrder;

class SubOrderController extends Controller
{
    public function show(SubOrder $suborder)
    {
        return $suborder;
    }
}
