<?php

namespace App\Http\Controllers;

use App\Func;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FunctionController extends Controller
{
    //
    public function getfuncs()
    {
        $data = Func::all();
        return $data;
    }
}
