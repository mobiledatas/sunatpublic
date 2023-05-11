<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //
    public function getCustomer($ruc)
    {
        $spc = new SharepointController;
        return $spc->getCustomer($ruc);
    }
}
