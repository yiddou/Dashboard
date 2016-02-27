<?php

namespace App\Http\Controllers;

use App\Company_dim;
use App\Daily_user_summary;
use App\Olap_index;
use App\Olap_user;
use App\UserInfo;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SnapshotController extends Controller
{
    const COURSE_STUDY = 'Course_Study';
    const COURSE_QUIZ = 'Course_Quiz';
    const LOGIN ='Login';
    const INDEX_ID_HOUR = 3;
    const INDEX_ID_MAU = 4;
    const INDEX_ID_MAUUTIL = 5;
    const INDEX_ID_USER = 1;
    const INDEX_ID_UTIL = 6;
    const INDEX_ID_NEWHIRE= 7;
    const INDEX_ID_HIREUTIL = 8;
    const INDEX_ID_TIME = 2;
    const INDEX_ID_RECRUIT = 9;

    public  function  Index($uid)
    {
        $snapshotarr = array();
        if($uid) {
            $lHours = 0;
            $lUsers = 0;
            $Users = 0;
            $util = 0;
            $mau = 0;
            $mauUtil = 0;
            $newHire = 0;
            $newHireUtil=0;
            $studyUsersNo = 0;
            $quizUserNo = 0;
            $quizTimes = 0;
            $resignNo = 0;
            $recruitNo = 0;

            $today = date("Ymd");
            $month = CommonFunc::getTheMonth($today);
            $userInfo= UserInfo::where('user_id','=',intval($uid))->first();
            $companyID  =  $userInfo['company_id'];


            $learningHours = Olap_index::where('company_id','=',intval($companyID))
                             ->whereIn('event_name',[self::COURSE_STUDY,self::COURSE_QUIZ])
                             ->where('index_id','=',self::INDEX_ID_HOUR)
                             ->where('olap_date','=',$today)
                             ->where('department','=','all')
                             ->where('department1','=','all')
                             ->where('department2','=','all')
                             ->where('department3','=','all')->get();


            $learningUsers = Olap_user::where('company_id','=',intval($companyID))
                             ->whereIn('event_name',[self::COURSE_STUDY,self::COURSE_QUIZ])
                             ->whereBetween('olap_date',array('20150102','20150103'))
                             ->distinct('user_id')->count();


            $userLogins = Olap_index::where('company_id','=',intval($companyID))
                ->where('event_name','=',self::LOGIN)
                ->where('index_id','=',self::INDEX_ID_USER)
                ->where('olap_date','=',$today)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->get();

            $totalUtil = Olap_index::where('company_id','=',intval($companyID))
                ->where('index_id','=',self::INDEX_ID_UTIL)
                ->where('olap_date','=',$today)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->first();

            $mauCount = Olap_index::where('company_id','=',intval($companyID))
                ->where('index_id','=',self::INDEX_ID_MAU)
                ->where('olap_date','=',$today)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->first();

            $mauUtilization = Olap_index::where('company_id','=',intval($companyID))
                ->where('index_id','=',self::INDEX_ID_MAUUTIL)
                ->where('olap_date','=',$today)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->first();


            $newHireEmployee = Olap_index::where('company_id','=',intval($companyID))
                ->where('index_id','=',self::INDEX_ID_NEWHIRE)
                ->where('olap_date','=',$today)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->first();

            $newHireUtilarr = Olap_index::where('company_id','=',intval($companyID))
                ->where('index_id','=',self::INDEX_ID_HIREUTIL)
                ->where('olap_date','=',$today)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->first();

            $studyUsers = Olap_index::where('company_id','=',intval($companyID))
                ->where('event_name','=',self::COURSE_STUDY)
                ->where('index_id','=',self::INDEX_ID_USER)
                ->where('olap_date','=',$today)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->first();

            $quizUsers = Olap_index::where('company_id','=',intval($companyID))
                ->where('event_name','=',self::COURSE_QUIZ)
                ->where('index_id','=',self::INDEX_ID_USER)
                ->where('olap_date','=',$today)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->first();


            $quizTimesNo= Olap_index::where('company_id','=',intval($companyID))
                ->where('event_name','=',self::COURSE_QUIZ)
                ->where('index_id','=',self::INDEX_ID_TIME)
                ->where('olap_date','=',$today)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->first();

            $recruitCount = Olap_index::where('company_id','=',intval($companyID))
                ->where('index_id','=',self::INDEX_ID_RECRUIT)
                ->where('olap_date','=',$today)
                ->where('department','=','all')
                ->where('department1','=','all')
                ->where('department2','=','all')
                ->where('department3','=','all')->first();


            $resignCount = Daily_user_summary::where('company_id','=',intval($companyID))
                           ->where('date_key','=',$today)->first();


            if(isset($learningHours)&&count($learningHours))
            {
                foreach($learningHours as $val)
                {
                    $lHours += intval($val['num_month']);
                }
            }


            if(isset($userLogins)&&count($userLogins))
            {
               foreach($userLogins as $val)
               {
                   $Users += intval($val['num_month']);
               }
            }

            if(isset($totalUtil)&&count($totalUtil))
            {
                $util = floatval($totalUtil['num_month']);
            }

            if(isset($mauCount)&&count($mauCount))
            {
                $mau = intval($mauCount['num_month']);
            }

            if(isset($mauUtilization)&&count($mauUtilization))
            {
                $mauUtil=floatval($mauUtilization['num_month']);
            }

            if(isset($newHireEmployee)&& count($newHireEmployee))
            {
                $newHire = intval($newHireEmployee['num_month']);
            }

            if(isset($newHireUtilarr)&&count($newHireUtilarr))
            {
                $newHireUtil=floatval($newHireUtilarr['num_month']);
            }

            if(isset($studyUsers)&&count($studyUsers))
            {
                $studyUsersNo = intval($studyUsers['num_month']);
            }

            if(isset($quizUsers)&&count($quizUsers))
            {
                $quizUserNo = intval($quizUsers['num_month']);
            }

            if(isset($quizTimesNo)&&count($quizTimesNo))
            {
                $quizTimes = intval($quizTimesNo['num_month']);
            }
            if(isset($resignCount)&&count($resignCount))
            {
                $resignNo  = intval($resignCount['monthly_resignation_count']);
            }
            if(isset($recruitCount)&&count($recruitCount))
            {
                $recruitNo = intval($recruitCount['num_month']);
            }

            $snapshotarr['learningHours'] = $lHours;
            $snapshotarr['learningUsers'] = $learningUsers;
            $snapshotarr['users'] = $Users ;
            $snapshotarr['util'] = $util;
            $snapshotarr['mau'] = $mau;
            $snapshotarr['mauUtil']=$mauUtil;
            $snapshotarr['newHire'] =$newHire;
            $snapshotarr['newHireUtil']=$newHireUtil;
            $snapshotarr['studyUserNo'] =$studyUsersNo;
            $snapshotarr['quizUserNo'] = $quizUserNo;
            $snapshotarr['quizTimes'] = $quizTimes;
            $snapshotarr['recruitNo'] = $recruitNo;
            $snapshotarr['resignNo'] = $resignNo;


            return $snapshotarr;

        }
        else
        {

        }
    }
}
