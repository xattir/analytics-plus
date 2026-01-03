<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nafezly\Payments\Classes\YallaPayPayment;

class BackendTestController extends Controller
{
    public function test(Request $request)
    {

       
        
        dd("TEST");
    }
    public function user(Request $request , \App\Models\User $user){
        dd($user);
    }
}
