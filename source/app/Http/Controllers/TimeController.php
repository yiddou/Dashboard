<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\UserInfo;

class TimeController extends Controller
{
    //
    public  function index($uid,Request $request)
    {
        $companyID = $request->get('cmpid', '');

        $userInfo = Validate::user($uid,$companyID);

        if($userInfo)
        {
            $weeks =array(1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday');

            $stdate = $request->get('stdate', '');
            $eddate = $request->get('eddate', '');

            $ltype = $request->get('ltype','');
            $day_week = $request->get('dweek','');
            $hour = $request->get('hour','');
            $department  = $request->get('dpm','');
            $eventid = $request->get('eventid','');

            $eventtype = '';
            $sql_select ='';
            if(intval($ltype) == 1)
            {
                $eventtype = 'spend_time';


            }elseif(intval($ltype) == 2)
            {
                $eventtype = 'num_of_times';
            }elseif(intval($ltype) == 3)
            {
                $eventtype = 'num_of_users';
            }

            if(!empty($department)&&!empty($day_week)&&!empty($hour) &&!empty($stdate) && !empty($eddate)&&!empty($eventid))
            {



                $sql_select = "select sum($eventtype) as num, concat(substring(b.day_week_name,1,3),hour) as day_name_hour from olap_event a, date_dim b
                               where olap_date = date_key
                               and olap_date between $stdate and $eddate";

                // department
                $arr = array();
                $arr = explode('_', $department);
                $str = '';
                for($i = 0;$i<count($arr)-1;$i++)
                {
                    $str .= "'$arr[$i]',";
                }
                $str .= "'".$arr[count($arr)-1]."'";
                $sql_select.=" and a.department in ($str) ";

                // day_week
                $temp_arr  = explode('_',$day_week);
                $temp_str ='';
                for($i = 0;$i<count($temp_arr)-1;$i++)
                {
                    $j = intval($temp_arr[$i]);
                    $temp_str = "'$weeks[$j]',";
                }
                $n = intval($temp_arr[count($temp_arr)-1]);
                $temp_str .= "'".$weeks[$n]."'";
                $sql_select.=" and b.day_week_name in ($temp_str) ";

                //hour
                $hour_arr = explode('_',$hour);
                $hour_str = implode(',',$hour_arr);
                $sql_select .=" and a.hour in ($hour_str) ";

                //event
                $sql_select .=" and a.event_id = $eventid ";


                //group by
                $sql = " group by concat(substring(b.day_week_name,1,3),hour) ";


                $data = DB::select($sql_select.$sql);

                return $data;

            }
            else
            {
                return array();
            }

        }
    }
}
