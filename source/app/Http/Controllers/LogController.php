<?php

namespace App\Http\Controllers;

use App\Trans_log;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    //
    public  function getlog()
    {
        $data = Trans_log::all();
        return $data;
    }
}
