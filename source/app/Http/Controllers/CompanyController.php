<?php

namespace App\Http\Controllers;

use App\Company_dim;
use App\UserInfo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    //
    public  function  getcompany($uid)
    {
        $data  =array();

        if($uid>0)
        {
            $arr  =array();
            $sql = "select a.company_id,b.company_en_name from user_info a join company_dim b on a.company_id = b.company_id where a.user_id = $uid";
            $data = DB::select($sql);
            $temp_arr = json_decode(json_encode($data), true);
            foreach( $temp_arr as $val)
            {
                $arr[$val['company_id']] = $val['company_en_name'];
            }
            $data = $arr;

        }
        elseif($uid=0)
        {
            $data = Company_dim::all()->pluck('company_en_name','company_id');
        }

        return  $data;
    }
}
