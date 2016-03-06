<?php

namespace App\Http\Controllers;

use App\UserInfo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    //
    public  function  getcompany($uid)
    {
        $data = UserInfo::where('user_id','=',$uid)
                ->where('status','=','1')
                ->pluck('company_name','company_id');

        return $data;
    }
}
