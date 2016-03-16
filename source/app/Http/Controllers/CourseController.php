<?php

namespace App\Http\Controllers;

use App\Olap_course;
use App\UserInfo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    //
    public  function index($uid,Request $request)
    {
        $companyID = $request->get('cmpid', '');
        $stdate = $request->get('stdate', '');
        $eddate = $request->get('eddate', '');

        $eventid = $request->get('eventid','');
        $ltype = $request->get('ltype','');
        $department  = $request->get('dpm','');
        $coursetype = $request->get('ctype','');
        $cateid = $request->get('cateid','');


        $userInfo = Validate::user($uid,$companyID);

        if($userInfo)
        {
            if(!empty($stdate) && !empty($eddate) && !empty($department) && !empty($eventid) && !empty($coursetype))
            {
                $learning_type = '';
                $sql_select = '';
                if($ltype == 1)
                {
                    $learning_type = 'day_spend_time';
                    $sql_select = " select company_name,course_id,course_name,sum($learning_type) as num from olap_course_intrim where olap_date between $stdate and $eddate and company_id = $companyID";
                }
                elseif($ltype ==2 )
                {
                    $learning_type = 'day_num_of_times';
                    $sql_select = " select company_name,course_id,course_name,sum($learning_type) as num from olap_course_intrim where olap_date between $stdate and $eddate and company_id = $companyID ";
                }
                elseif($ltype == 3) {

                    $sql_user = "select DISTINCT user_id from user_dim where company_id = $companyID ";
                    $dataarr= array();
                    if($department !='all') {
                        $arr = explode('_', $department);
                        $str = '';
                        for($i = 0;$i<count($arr)-1;$i++)
                        {
                            $str .= "'$arr[$i]',";
                        }
                        $str .= "'".$arr[count($arr)-1]."'";

                        $sql_user .= " and  department in ($str) ";
                        $dataarr = DB::select($sql_user);
                    }
                    else
                    {
                        $dataarr = DB::select($sql_user);
                    }
                    if(isset($dataarr)&&count($dataarr))
                    {

                        $str_user = '';
                        $user_arr = array();
                        $temp_arr = json_decode(json_encode($dataarr), true);

                        foreach($temp_arr as $val)
                        {
                            array_push($user_arr,$val['user_id']);
                        }
                        $str_user = implode(',',$user_arr);

                        $sql_select = " select company_name,course_id,course_name,count(distinct user_id) as num from event_summary_fact where event_date between $stdate and $eddate and user_id in ($str_user) ";

                    }

                }
                else
                {
                    $learning_type = 'day_spend_time';
                    $sql_select = " select company_name,course_id,course_name,sum($learning_type) as num from olap_course_intrim where olap_date between $stdate and $eddate ";
                }



                $sql = 'group by course_id order by num desc limit 10 ';

                if(!empty($eventid))
                {
                    $arr =  explode('_', $eventid);
                    $str =  implode(',',$arr);
                    $sql_select .= " and event_id in ($str) ";
                }
                else
                {
                    $sql_select .= " and event_id in (1,2) ";
                }

                if($ltype == 1 || $ltype == 2)
                {
                    $arr = array();
                    $arr = explode('_', $department);
                    $str = '';
                    for($i = 0;$i<count($arr)-1;$i++)
                    {
                        $str .= "'$arr[$i]',";
                    }
                    $str .= "'".$arr[count($arr)-1]."'";
                    $sql_select.=" and department in ($str) ";
                }


                if(!empty($coursetype))
                {
                    $num = intval($coursetype);
                    $sql_select.= " and course_type = $num ";
                }

                if(intval($cateid))
                {
                    $sql_select .= " and category_id =$cateid ";
                }


                $data = DB::select($sql_select.$sql);
                return $data;
            }
            else
            {
                return array();
            }
        }

    }


    public function getCourse($uid,Request $request)
    {

        $companyid= $request->get('cmpid','');



        if(isset($uid)&&intval($companyid))
        {

            $data  = Olap_course::where('course_type','=',$uid)
                     ->where('company_id','=',$companyid)->distinct('course_id')->pluck('course_name','course_id');

            return $data;
        }

    }

}
