<?php
/**
 * Created by PhpStorm.
 * User: baidu
 * Date: 16/3/8
 * Time: 下午12:54
 */
namespace App\Http\Controllers;
use App\User_mgr;
use App\UserInfo;

class Validate
{
    public  static function user($uid,$cmpid)
    {
        $result  =false;
        $userInfo = array();
        if($uid > 0) {

            $userInfo =User_mgr::where('is_deleted','=',0)
                       ->where('status','=',1)
                       ->first();
            if(count($userInfo))
            {
                $data = UserInfo::where('user_id', '=', intval($uid))
                    ->where('company_id', '=', intval($cmpid))
                    ->where('status', '=', 1)
                    ->first();
                if(count($data))
                {
                    $result = true;
                }
            }

        }
        elseif($uid == 0)
        {
            $result = true;
        }

        return $result;
    }
}