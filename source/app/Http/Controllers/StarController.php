<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StarController extends Controller
{
    //
    public  function  index($uid,Request $request)
    {
        $companyid = $request->get('cmpid', '');
        $limit  = $request->get('limit','0');
        $stdate = $request->get('stdate', '');
        $eddate = $request->get('eddate', '');

        $ltype = $request->get('ltype','');
        $eventid = $request->get('eventid','');
        $department  = $request->get('dpm','all');
        $coursetype = $request->get('ctype','');
        $cateid = $request->get('cateid','');
        $coureid = $request->get('cid','');


        if(isset($companyid))
        {
            $sql_user = "select DISTINCT user_id,employee_id,realname,department from user_dim where company_id = $companyid ";
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
                $userinfo_arr =array();
                $temp_arr = json_decode(json_encode($dataarr), true);

                foreach($temp_arr as $val)
                {
                    array_push($user_arr,$val['user_id']);
                    $userinfo_arr[$val['user_id']] = array('employee_id'=>$val['employee_id'],'realname'=>$val['realname'],'department'=>$val['department']);

                }
                $str_user = implode(',',$user_arr);


                $sql_select  = '';
                $learning_type = '';

                if(empty($coureid))
                {
                    if($ltype == 1) {

                        $learning_type  = 'spend_time';
                        $sql_select = " select user_id,company_name,department,sum($learning_type) as num from olap_user where olap_date between $stdate and $eddate and user_id in ($str_user) ";
                    }
                    else
                    {
                        $learning_type  = 'num_of_times';
                        $sql_select = " select user_id,company_name,department,sum($learning_type) as num from olap_user where olap_date between $stdate and $eddate and user_id in ($str_user) ";
                    }

                    if(!empty($eventid))
                    {
                        $arr =  explode('_', $eventid);
                        $event_name = "'Course_Study','Course_Quiz'";
                        if(count($arr)>=2) {
                        }
                        elseif(count($arr))
                        {
                            if(intval($arr[0]) == 1)
                            {
                                $event_name = "'Course_Study'";
                            }
                            elseif(intval($arr[0]) == 2)
                            {
                                $event_name = "'Course_Quiz'";
                            }
                        }
                        $sql_select .= " and event_name in ($event_name) ";
                    }
                    else
                    {
                        $sql_select .= " and event_id in ('Course_Study','Course_Quiz') ";
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

                    $sql = "group by user_id order by num desc limit $limit ";

                    $data = DB::select($sql_select.$sql);

                    $realdata =array();
                    if(isset($data) && count($data))
                    {
                        $temp_arr = json_decode(json_encode($data), true);
                        foreach($temp_arr as $val)
                        {
                            $temp = array();
                            $temp['user_id'] = $val['user_id'];
                            $temp['company'] = $val['company_name'];
                            $temp['employee_id'] = $userinfo_arr[$val['user_id']]['employee_id'];
                            $temp['name'] = $userinfo_arr[$val['user_id']]['realname'];
                            $temp['department'] = $userinfo_arr[$val['user_id']]['department'];
                            $temp['num'] = $val['num'];

                            array_push($realdata,$temp);
                        }
                    }
                    return $realdata;

                }
                else
                {
                    if($ltype == 1) {

                        $learning_type  = 'spend_time';
                        $sql_select = " select user_id,company_name,sum($learning_type) as num from event_summary_fact where event_date between $stdate and $eddate and user_id in ($str_user) and course_id = $coureid";
                    }
                    else
                    {
                        $sql_select = " select user_id,company_name,count(user_id) as num from event_summary_fact where event_date between $stdate and $eddate and user_id in ($str_user) and course_id = $coureid ";
                    }

                    if(!empty($eventid))
                    {
                        $arr =  explode('_', $eventid);
                        $str =  implode(',',$arr);
                        $sql_select .= " and event_id in ($str) ";
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

                    $sql = "group by user_id order by num desc limit $limit ";

                    $data = DB::select($sql_select.$sql);

                    $realdata =array();
                    if(isset($data) && count($data))
                    {
                        $temp_arr = json_decode(json_encode($data), true);
                        foreach($temp_arr as $val)
                        {
                            $temp = array();
                            $temp['user_id'] = $val['user_id'];
                            $temp['company'] = $val['company_name'];
                            $temp['employee_id'] = $userinfo_arr[$val['user_id']]['employee_id'];
                            $temp['name'] = $userinfo_arr[$val['user_id']]['realname'];
                            $temp['department'] = $userinfo_arr[$val['user_id']]['department'];
                            $temp['num'] = $val['num'];

                            array_push($realdata,$temp);
                        }
                    }
                    return $realdata;

                }




            }
        }


    }
}
