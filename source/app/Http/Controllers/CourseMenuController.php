<?php

namespace App\Http\Controllers;

use App\Category_dim;
use App\Department_dim;
use App\Event_dim;
use App\UserInfo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CourseMenuController extends Controller
{
    //
    public  function  index($uid)
    {
        $userInfo= UserInfo::where('user_id','=',intval($uid))->first();
        $companyID  =  $userInfo['company_id'];

        $data = array();

        $eventarr = Event_dim::whereIN('event_id',array(1,2))->pluck('event_name','event_id');

        $data['action_name'] = $eventarr;
        $data['department'] = Department_dim::where('company_id','=',$companyID)->distinct('department')->pluck('department');

        $data['parent_category'] = Category_dim::where('company_id','=',$companyID)
                                    ->distinct('parent_category_id')->pluck('parent_category_name','parent_category_id');

        $data['coures_type'][1] = 'general';
        $data['coures_type'][0] ='customized';



        return $data;

    }

}
