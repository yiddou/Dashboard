<?php

namespace App\Http\Controllers;

use App\Date_dim;
use App\UserInfo;
use App\Olap_index;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TrendController extends Controller
{
    const INDEX_ID_USER = 1;
    const INDEX_ID_TIME = 2;
    const INDEX_ID_HOUR = 3;
    const INDEX_ID_UTIL = 6;
    const INDEX_ID_MAU = 4;
    const INDEX_ID_MAUUTIL = 5;
    const INDEX_ID_NEWHIRE = 7;
    const INDEX_ID_HIREUTIL = 8;
    const INDEX_ID_RECRUIT = 9;
    const INDEX_ID_LEARNINGHOURS = 10;
    const INDEX_ID_PATICIPATE = 11;

    const COURSE_STUDY = 'Course_Study';
    const COURSE_QUIZ = 'Course_Quiz';
    const PK = 'PK';
    const MAP_QUIZ ='Map_Quiz';

    const SUNDAY = 'Sunday';

    public function  Index($uid, Request $request)
    {
        $metrics = $request->get('metrics', '');;
        $stdate = $request->get('stdate', '');
        $eddate = $request->get('eddate', '');
        $dtype = $request->get('dtype', '');
        $contrast = $request->get('ctr', '');
        $trendData = array();
        $eventName = '';
        if ($uid && isset($metrics) && isset($stdate) && isset($eddate) && isset($dtype) && isset($contrast)) {

            $userInfo = UserInfo::where('user_id', '=', intval($uid))->first();
            $companyID = $userInfo['company_id'];
            $metricsArr = explode('_', $metrics);
            if (count($metricsArr)) {
                foreach ($metricsArr as $val) {
                    if ($val == 'lhours') {
                        $data = $this->getData(self::INDEX_ID_LEARNINGHOURS,$stdate,$eddate,$eventName,$companyID,$dtype,$contrast);
                        $trendData['learningHours'] = $data;
                    }
                    elseif($val == 'mau')
                    {
                        if($contrast == 'mom') {
                            $data = $this->getData(self::INDEX_ID_MAU, $stdate, $eddate, $eventName, $companyID, $dtype, $contrast);
                            $trendData['mau'] = $data;
                        }
                        else
                        {
                            $trendData['mau'] = array();
                        }
                    }
                    elseif($val == 'mutil')
                    {
                        $data = $this->getData(self::INDEX_ID_MAUUTIL, $stdate, $eddate, $eventName, $companyID, $dtype, $contrast);
                        $trendData['mauUtil'] = $data;
                    }
                    elseif($val == 'tutil')
                    {
                        $data = $this->getData(self::INDEX_ID_UTIL, $stdate, $eddate, $eventName, $companyID, $dtype, $contrast);
                        $trendData['totalUtil'] = $data;
                    }
                    elseif($val == 'nhire')
                    {
                        $data = $this->getData(self::INDEX_ID_NEWHIRE, $stdate, $eddate, $eventName, $companyID, $dtype, $contrast);
                        $trendData['newHire'] = $data;
                    }
                    elseif($val == 'nhutil')
                    {
                        $data = $this->getData(self::INDEX_ID_HIREUTIL, $stdate, $eddate, $eventName, $companyID, $dtype, $contrast);
                        $trendData['newHire'] = $data;
                    }
                    elseif($val == 'stuser')
                    {
                        $data = $this->getData(self::INDEX_ID_USER, $stdate, $eddate,self::COURSE_STUDY, $companyID, $dtype, $contrast);
                        $trendData['studyUserNo'] = $data;
                    }
                    elseif($val =='quser')
                    {
                        $data = $this->getData(self::INDEX_ID_USER, $stdate, $eddate,self::COURSE_QUIZ, $companyID, $dtype, $contrast);
                        $trendData['quizUserNo'] = $data;
                    }
                    elseif($val == 'pkuser')
                    {
                        $data = $this->getData(self::INDEX_ID_USER, $stdate, $eddate,self::PK, $companyID, $dtype, $contrast);
                        $trendData['pkUserNo'] = $data;
                    }
                    elseif($val == 'cluser')
                    {
                        $data = $this->getData(self::INDEX_ID_USER, $stdate, $eddate,self::MAP_QUIZ, $companyID, $dtype, $contrast);
                        $trendData['challengeUserNo'] = $data;
                    }

                }
            }
            return $trendData;
        }
    }


    private function getData($indexid,$startdate,$enddate,$eventName='',$companyId,$date,$contrast)
    {
        $data = array();
        $arr = array();
        if(empty($eventName))
        {
            $data= Olap_index::where('company_id','=',intval($companyId))
                ->where('index_id','=',$indexid)
                ->whereBetween('olap_date',array($startdate,$enddate))
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')
                ->distinct('olap_date')
                ->orderBy('olap_date', 'asc')->get();
        }
        else
        {
            $data= Olap_index::where('company_id','=',intval($companyId))
                ->where('event_name','=',$eventName)
                ->where('index_id','=',$indexid)
                ->whereBetween('olap_date',array($startdate,$enddate))
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')
                ->distinct('olap_date')
                ->orderBy('olap_date', 'asc')->get();
        }

        if(isset($data)&&count($data))
        {
            if($date == 'day')
            {
                foreach($data as $val)
                {
                    $arr[$val['olap_date']] = $val[$contrast.'_'.$date];
                }
            }
            elseif($date == 'week')
            {
                $i = 0;
                foreach($data as $val)
                {
                    $i++;
                    $datearr =Date_dim::where('date_key','=',$val['olap_date'])
                              ->where('WEEKEND_FLAG','=','Y')
                              ->where('DAY_WEEK_NAME','=','Sunday')->count();
                    if($datearr)
                    {
                        $arr[$val['olap_date']] = $val[$contrast.'_'.$date];
                    }
                }
                $arr[$data[$i-1]['olap_date']] = $data[$i-1][$contrast.'_'.$date];
            }
            elseif($date == 'month')
            {
                $i = 0;
                foreach($data as $val)
                {
                    $i++;
                    $datearr =Date_dim::where('date_key','=',$val['olap_date'])
                        ->where('END_MONTH_FLAG','=','Y')
                        ->count();
                    if($datearr)
                    {
                        $arr[$val['olap_date']] = $val[$contrast.'_'.$date];
                    }
                }
                $arr[$data[$i-1]['olap_date']] = $data[$i-1][$contrast.'_'.$date];
            }
            elseif($date == 'quarter')
            {
                $i = 0;
                foreach($data as $val)
                {
                    $i++;
                    $datearr =Date_dim::where('date_key','=',$val['olap_date'])
                        ->where('END_QUARTER_FLAG','=','Y')
                        ->count();
                    if($datearr)
                    {
                        $arr[$val['olap_date']] = $val[$contrast.'_'.$date];
                    }
                }
                $arr[$data[$i-1]['olap_date']] = $data[$i-1][$contrast.'_'.$date];
            }

        }
        return $arr;
    }
}
