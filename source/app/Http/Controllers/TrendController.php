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
        $companyID = $request->get('cmpid', '');
        $trendData = array();
        $eventName = '';
        if ($uid && isset($metrics) && isset($stdate) && isset($eddate) && isset($dtype) && isset($contrast)) {

            $userInfo = UserInfo::where('user_id', '=', intval($uid))
                ->where('company_id', '=', intval($companyID))
                ->where('status', '=', 1)
                ->first();

            if (isset($userInfo) && count($userInfo)) {
                $metricsArr = explode('_', $metrics);
                if (count($metricsArr)) {
                    foreach ($metricsArr as $val) {
                        if ($val == 'lhours') {

                            $data = $this->getData(1, $stdate, $eddate, $eventName, $companyID, $dtype, $contrast);
                            $trendData['learningHours']['src'] = $data;
                            if ($contrast == 'yoy' || $contrast == 'mom') {
                                $arr = $this->getDateFunc($dtype, $contrast, $stdate, $eddate);
                                $stdate_c = $arr['start'];
                                $eddate_c = $arr['end'];
                                $dataNew = $data = $this->getData(1, $stdate_c, $eddate_c, $eventName, $companyID, $dtype, $contrast);
                                $trendData['learningHours']['new'] = $dataNew;
                            }

                        } elseif ($val == 'mau') {
                            $data = $this->getData(self::INDEX_ID_MAU, $stdate, $eddate, $eventName, $companyID, $dtype, $contrast);
                            $trendData['mau']['src'] = $data;
                            if ($contrast == 'mom' || $contrast == 'yoy') {
                                $arr = $this->getDateFunc($dtype, $contrast, $stdate, $eddate);
                                $stdate_c = $arr['start'];
                                $eddate_c = $arr['end'];
                                $data = $this->getData(self::INDEX_ID_MAU, $stdate_c, $eddate_c, $eventName, $companyID, $dtype, $contrast);
                                $trendData['mau']['new'] = $data;
                            }

                        } elseif ($val == 'mutil') {
                            $data = $this->getData(self::INDEX_ID_MAUUTIL, $stdate, $eddate, $eventName, $companyID, $dtype, $contrast);
                            $trendData['mauUtil']['src'] = $data;
                            if ($contrast == 'mom' || $contrast == 'yoy') {
                                $arr = $this->getDateFunc($dtype, $contrast, $stdate, $eddate);
                                $stdate_c = $arr['start'];
                                $eddate_c = $arr['end'];
                                $data = $this->getData(self::INDEX_ID_MAUUTIL, $stdate_c, $eddate_c, $eventName, $companyID, $dtype, $contrast);
                                $trendData['mauUtil']['new'] = $data;
                            }

                        } elseif ($val == 'tutil') {
                            $data = $this->getData(self::INDEX_ID_UTIL, $stdate, $eddate, $eventName, $companyID, $dtype, $contrast);
                            $trendData['totalUtil']['src'] = $data;
                            if ($contrast == 'mom' || $contrast == 'yoy') {
                                $arr = $this->getDateFunc($dtype, $contrast, $stdate, $eddate);
                                $stdate_c = $arr['start'];
                                $eddate_c = $arr['end'];
                                $data = $this->getData(self::INDEX_ID_UTIL, $stdate_c, $eddate_c, $eventName, $companyID, $dtype, $contrast);
                                $trendData['totalUtil']['new'] = $data;
                            }

                        } elseif ($val == 'nhire') {
                            $data = $this->getData(self::INDEX_ID_NEWHIRE, $stdate, $eddate, $eventName, $companyID, $dtype, $contrast);
                            $trendData['newHire']['src'] = $data;
                            if ($contrast == 'mom' || $contrast == 'yoy') {
                                $arr = $this->getDateFunc($dtype, $contrast, $stdate, $eddate);
                                $stdate_c = $arr['start'];
                                $eddate_c = $arr['end'];
                                $data = $this->getData(self::INDEX_ID_NEWHIRE, $stdate_c, $eddate_c, $eventName, $companyID, $dtype, $contrast);
                                $trendData['newHire']['new'] = $data;
                            }
                        } elseif ($val == 'nhutil') {
                            $data = $this->getData(self::INDEX_ID_HIREUTIL, $stdate, $eddate, $eventName, $companyID, $dtype, $contrast);
                            $trendData['newHireUtil']['src'] = $data;

                            if ($contrast == 'mom' || $contrast == 'yoy') {
                                $arr = $this->getDateFunc($dtype, $contrast, $stdate, $eddate);
                                $stdate_c = $arr['start'];
                                $eddate_c = $arr['end'];
                                $data = $this->getData(self::INDEX_ID_HIREUTIL, $stdate_c, $eddate_c, $eventName, $companyID, $dtype, $contrast);
                                $trendData['newHireUtil']['new'] = $data;
                            }

                        } elseif ($val == 'stuser') {
                            $data = $this->getData(self::INDEX_ID_USER, $stdate, $eddate, self::COURSE_STUDY, $companyID, $dtype, $contrast);
                            $trendData['studyUserNo']['src'] = $data;
                            if ($contrast == 'mom' || $contrast == 'yoy') {
                                $arr = $this->getDateFunc($dtype, $contrast, $stdate, $eddate);
                                $stdate_c = $arr['start'];
                                $eddate_c = $arr['end'];
                                $data = $this->getData(self::INDEX_ID_USER, $stdate_c, $eddate_c, self::COURSE_STUDY, $companyID, $dtype, $contrast);
                                $trendData['studyUserNo']['new'] = $data;
                            }
                        } elseif ($val == 'quser') {
                            $data = $this->getData(self::INDEX_ID_USER, $stdate, $eddate, self::COURSE_QUIZ, $companyID, $dtype, $contrast);
                            $trendData['quizUserNo']['src'] = $data;
                            if ($contrast == 'mom' || $contrast == 'yoy') {
                                $arr = $this->getDateFunc($dtype, $contrast, $stdate, $eddate);
                                $stdate_c = $arr['start'];
                                $eddate_c = $arr['end'];
                                $data = $this->getData(self::INDEX_ID_USER, $stdate_c, $eddate_c, self::COURSE_QUIZ, $companyID, $dtype, $contrast);
                                $trendData['quizUserNo']['new'] = $data;
                            }
                        } elseif ($val == 'pkuser') {
                            $data = $this->getData(self::INDEX_ID_USER, $stdate, $eddate, self::PK, $companyID, $dtype, $contrast);
                            $trendData['pkUserNo']['src'] = $data;
                            if ($contrast == 'mom' || $contrast == 'yoy') {
                                $arr = $this->getDateFunc($dtype, $contrast, $stdate, $eddate);
                                $stdate_c = $arr['start'];
                                $eddate_c = $arr['end'];
                                $data = $this->getData(self::INDEX_ID_USER, $stdate_c, $eddate_c, self::PK, $companyID, $dtype, $contrast);
                                $trendData['pkUserNo']['new'] = $data;
                            }

                        } elseif ($val == 'cluser') {
                            $data = $this->getData(self::INDEX_ID_USER, $stdate, $eddate, self::MAP_QUIZ, $companyID, $dtype, $contrast);
                            $trendData['challengeUserNo']['src'] = $data;
                            if ($contrast == 'mom' || $contrast == 'yoy') {
                                $arr = $this->getDateFunc($dtype, $contrast, $stdate, $eddate);
                                $stdate_c = $arr['start'];
                                $eddate_c = $arr['end'];
                                $data = $this->getData(self::INDEX_ID_USER, $stdate_c, $eddate_c, self::MAP_QUIZ, $companyID, $dtype, $contrast);
                                $trendData['challengeUserNo']['new'] = $data;
                            }
                        }

                    }
                }
                return $trendData;
            }
        }
    }


        private function getData($indexid,$startdate,$enddate,$eventName='',$companyId,$date,$contrast)
        {
        $constNum = 'num';
        $data = array();
        $arr = array();
            $daynums = $this->getdaysnum($startdate,$enddate);

            if($date == 'day')
            {
                $day = $startdate;
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
                $temarr = array();
                foreach($data as $val)
                {
                    $temarr[$val['olap_date']] = $val[$constNum.'_'.$date];

                }

                for($i = 0;$i<$daynums;$i++)
                {

                    if(isset($temarr[$day]))
                    {
                        $arr[$day]  =floatval($temarr[$day]);
                    }
                    else
                    {
                        $arr[$day] = 0;
                    }
                    $day = date('Ymd',strtotime("$day +1 day"));
                }

            }
            elseif($date == 'week')
            {
                $day = $startdate;
                $daynums = $this->getdaysnum($startdate,$enddate);

                for($i = 0;$i<$daynums;$i++) {

                    $result = $this->isWeekend($day);
                    if($result)
                    {
                        $data = $this->getdatabyday($companyId,$indexid,$eventName,$day);
                        if(isset($data)&&count($data))
                        {
                            $arr[$day] = floatval($data[$constNum.'_'.$date]);
                        }
                        else
                        {
                            $arr[$day] = 0;
                        }
                    }
                    $day = date('Ymd',strtotime("$day +1 day"));
                }
                $datae =  $this->getdatabyday($companyId,$indexid,$eventName,$enddate);
                if(isset($datae)&&count($datae))
                {
                    $arr[$enddate] = $datae[$constNum.'_'.$date];
                }
                else
                {
                    $arr[$enddate] = 0;
                }

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
                        $arr[$val['olap_date']] = $val[$constNum.'_'.$date];
                    }
                }
                $arr[$data[$i-1]['olap_date']] = $data[$i-1][$constNum.'_'.$date];
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
                        $arr[$val['olap_date']] = $val[$constNum.'_'.$date];
                    }
                }
                $arr[$data[$i-1]['olap_date']] = $data[$i-1][$constNum.'_'.$date];
            }

            return $arr;

        }



    private function getDateFunc($type='day',$contrast='df',$start,$end)
   {
       $start_str ='start';
       $end_str = 'end';
       $data = array();
      if($type == 'day')
      {
          $arr = $this->getDay($start,$end);
          $data['start'] = $arr[$contrast.'_'.$start_str];
          $data['end'] = $arr[$contrast.'_'.$end_str];
      }
      elseif($type == 'week')
      {
          $arr = $this->getWeek($start,$end);
          $data['start'] = $arr[$contrast.'_'.$start_str];
          $data['end'] = $arr[$contrast.'_'.$end_str];
      }
      elseif($type == 'month')
      {
          $arr = $this->getMonth($start,$end);
          $data['start'] = $arr[$contrast.'_'.$start_str];
          $data['end'] = $arr[$contrast.'_'.$end_str];

      }
      elseif($type =='quarter')
       {
           $arr = $this->getQuarter($start,$end);
           $data['start'] = $arr[$contrast.'_'.$start_str];
           $data['end'] = $arr[$contrast.'_'.$end_str];
       }
       return $data;
   }

    private function  getDay($start,$end)
    {
        $data  =array();
        $data['df_start'] = $start;
        $data['df_end'] = $end;
        $data['mom_start'] = date('Ymd',strtotime('-1 month',strtotime(strval($start))));
        $data['mom_end'] = date('Ymd',strtotime('-1 month',strtotime(strval($end))));
        $data['yoy_start'] = date('Ymd',strtotime('-1 year',strtotime(strval($start))));
        $data['yoy_end'] = date('Ymd',strtotime('-1 year',strtotime(strval($end))));
        return $data;
    }
    private function  getWeek($start,$end)
    {
        $data = array();
        $data['df_start'] = $start;
        $data['df_end'] = $end;
        $lastStartDate  = date('Ymd',strtotime("$start -7 day"));
        $lastEndtDate  = date('Ymd',strtotime("$end -7 day"));
        $data['mom_start'] = $lastStartDate;
        $data['mom_end'] = $lastEndtDate;
        $yearStartDate  = date('Ymd',strtotime("$start -1 year"));
        $yearEndtDate  = date('Ymd',strtotime("$end -1 year"));
        $data['yoy_start'] = $yearStartDate;
        $data['yoy_end'] = $yearEndtDate;
        return $data;
    }

    private function getMonth($start,$end)
    {
        $keyarr  = array(1=>31,2=>28,3=>31,4=>30,5=>31,6>30,7=>31,
            8=>31,9=>30,10=>31,11=>30,12=>31,0=>31);
        $data = array();

        $data['df_start'] = $start;
        $data['df_end'] = $end;
        $firstday = date('Ym01',strtotime($start));
        $endday =date('Ym01',strtotime($end));

        $lastsm = intval(date('m',strtotime($start)));
        $lastsd = intval(date('d',strtotime($start)));
        $lastem = intval(date('m',strtotime($end)));
        $lasted = intval(date('d',strtotime($end)));
        if($keyarr[$lastsm-1] > $lastsd )
        {
            $data['mom_start'] = date('Ymd',strtotime("$start -1 month"));
        }
        else
        {
            $data['mom_start'] = date('Ymd',strtotime("$firstday -1 day"));
        }
        if($keyarr[$lastem-1] > $lasted )
        {
            $data['mom_end'] = date('Ymd',strtotime("$end -1 month"));
        }
        else
        {
            $data['mom_end'] = date('Ymd',strtotime("$endday -1 day"));
        }

          $data['yoy_start'] = date('Ymd',strtotime("$start -1 year"));
          $data['yoy_end'] = date('Ymd',strtotime("$end -1 year"));
          return $data;

    }

    private  function getQuarter($start,$end)
    {
        $keyarr  = array(1=>31,2=>28,3=>31,4=>30,5=>31,6>30,7=>31,
            8=>31,9=>30,10=>31,11=>30,12=>31,0=>31,-1=>30,-2=>31);

        $data['df_start'] = $start;
        $data['df_end'] = $end;
        $firstday = date('Ym01',strtotime($start));
        $endday =date('Ym01',strtotime($end));

        $lastsm = intval(date('m',strtotime($start)));
        $lastsd = intval(date('d',strtotime($start)));
        $lastem = intval(date('m',strtotime($end)));
        $lasted = intval(date('d',strtotime($end)));
        if($keyarr[$lastsm-3] > $lastsd )
        {
            $data['mom_start'] = date('Ymd',strtotime("$start -3 month"));
        }
        else
        {
            $lqslastday = date('Ymd',strtotime("$firstday -2 month"));
            $data['mom_start'] = date('Ymd',strtotime("$lqslastday -1 day"));
        }
        if($keyarr[$lastem-3] > $lasted )
        {
            $data['mom_end'] = date('Ymd',strtotime("$end -3 month"));
        }
        else
        {
            $lqelastday = date('Ymd',strtotime("$endday -2 month"));
            $data['mom_end'] = date('Ymd',strtotime("$lqelastday -1 day"));
        }

        $data['yoy_start'] = date('Ymd',strtotime("$start -1 year"));
        $data['yoy_end'] = date('Ymd',strtotime("$end -1 year"));

        return $data;

    }

    private  function  getdaysnum($start,$end)
    {
        $startdate=strtotime($start);
        $enddate=strtotime($end);
        $days=intval(($enddate-$startdate)/3600/24);

        return $days;
    }

    private  function isWeekend ($date)
    {
        $weekend  = date('Ymd',mktime(23,59,59,date('m',strtotime($date)),date('d',strtotime($date))-date('w',strtotime($date))+7-7,date('Y',strtotime($date))));

        if(intval($date) == intval($weekend))
        {
            return true;
        }
        return false;
    }

    private  function  isMonthend($date)
    {
        $date = $date('Ymd',strtotime($date));
        $tmp = $date('Ym01',strtotime("$date +1 month"));
        $monthend = $date('Ymd',strtotime("$tmp -1 day"));
        if(intval($date) == intval($monthend))
        {
            return true;
        }
        return false;
    }

    private function  isQuarterend($date)
    {
        $month  = intval(date('m',strtotime($date)));
        $day  = intval(date('d',strtotime($date)));
        $arr = array(3=>31,6=>30,9=>30,12=>31);
        if(isset($arr[$month]))
        {
            if($day == $arr[$month] )
            {
                return true;
            }
            return false;
        }
        return false;
    }

    private function  getdatabyday($companyId,$indexid,$eventName,$day)
    {
        $data = array();
        if(empty($eventName))
        {
            $data= Olap_index::where('company_id','=',intval($companyId))
                ->where('index_id','=',$indexid)
                ->where('olap_date','=',$day)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->first();
        }
        else
        {
            $data= Olap_index::where('company_id','=',intval($companyId))
                ->where('event_name','=',$eventName)
                ->where('index_id','=',$indexid)
                ->where('olap_date','=',$day)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->first();
        }
        return $data;
    }

}
