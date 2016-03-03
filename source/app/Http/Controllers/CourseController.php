<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    //
    public  function index($uid,Request $request)
    {
        $stdate = $request->get('stdate', '');
        $eddate = $request->get('eddate', '');
        $cateid = $request->get('cateid','');
        $actinname = $request->get('cat','');
        $type = $request->get('type','');
        $catetype = $request->get('catetype','');
        $department  = $request->get('dpm','all');

        if(!empty($stdate) && !empty($eddate))
        {

            $data = DB::select('');
            return $data;
        }
    }
}
