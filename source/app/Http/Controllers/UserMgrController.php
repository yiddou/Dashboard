<?php

namespace App\Http\Controllers;

use App\Role;
use App\Role_function;
use App\User;
use App\User_mgr;
use App\User_role;
use App\UserInfo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserMgrController extends Controller
{
    //
    public function  insertuser(Request $request)
    {
        $name = $request->input('name', '');
        $employee_id = $request->input('empid', '');
        $department = $request->input('dpm', '');
        $realname = $request->input('rname','');
        $status  = $request->input('status','');
        $role_id = $request->input('roleid', '');
        $company_ids = $request->input('cmpid', '');
        $type = $request->input('type','');
        $user_info = User_mgr::where('name', '=', $name)->first();

        if (isset($user_info) && count($user_info)) {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH . ErrorCode::SAME_USRNAME;
            return $data;
        } else {
            $data = array();
            $user = new User_mgr();
            $user->name = $name;
            $user->password = Crypt::php_encrypt($name);
            $user->employee_id = $employee_id;
            $user->department = $department;
            $user->realname  =$realname;
            $user->status = $status;
            $user->type = $type;
            $user->update_date = date('Y-m-d h:i:s',time()+8*3600);
            $user->save();
            $userid = $user->id;


            return array('result'=>'success');

        }
    }

    public  function  assignrole($uid,Request $request)
    {
        $role_id = $request->input('roleid', '');
        $status  = $request->input('status','');
        if (!empty($role_id)&&!empty($status)) {
            $sql ="delete from user_role where user_id =$uid ";
            DB::delete($sql);
            $role_user = new User_role();
            $role_user->user_id = $uid;
            $role_user->role_id = $role_id;
            $role_user->status = $status;
            $role_user->save();
            return array('result'=>'success');
        }
        else
        {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH .ErrorCode::INVALID_PARAM;
            return $data;
        }
    }

    public  function  assigncompanys($uid,Request $request)
    {
        $status  = $request->input('status','');
        $company_ids = $request->input('cmpid','');
        if (!empty($company_ids)) {
            $arr = explode('_', $company_ids);
            $sql  = "delete from user_info where user_id = $uid";
            DB::delete($sql);
            foreach ($arr as $val) {
                $userinfo = new UserInfo();
                $userinfo->user_id = $uid;
                $userinfo->company_id = $val;
                $userinfo->status = $status;
                $userinfo->save();
            }
            return array('result'=>'success');
        }
        else
        {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH .ErrorCode::INVALID_PARAM;
            return $data;
        }
    }

        public function login(Request $request)
        {
            $name = $request->input('name', '');
            $password = $request->input('pwd','');

            $arr = array();

            if(!empty($name)&&!empty($password))
            {
                $data = User_mgr::where('name','=',$name)
                        ->where('is_deleted','=',0)
                        ->where('status','=',1)
                        ->first();
                if(isset($data)&&count($data))
                {
                    if(Crypt::php_decrypt($data['password']) == $password )
                    {
                        $arr['id'] = $data['id'];
                        $arr['name'] = $data['name'];
                        $arr['employee_id'] = $data['employee_id'];
                        $arr['department']  = $data['department'];
                        $arr['realname'] = $data['realname'];
                        $arr['type'] = $data['type'];

                        $roledata = User_role::where('user_id','=',$data['id'])
                            ->where('status','=',1)
                            ->first();
                        $functiondata = Role_function::where('role_id','=',$roledata['role_id'])->lists('function_id');
                        $arr['function'] = $functiondata;

                        $user_id = $data['id'];
                        $temp  =array();
                        $sql = "select a.company_id,b.company_en_name from user_info a join company_dim b on a.company_id = b.company_id where a.user_id = $user_id and a.status = 1";
                        $data = DB::select($sql);
                        $temp_arr = json_decode(json_encode($data), true);
                        foreach( $temp_arr as $val)
                        {
                            $temp[$val['company_id']] = $val['company_en_name'];
                        }
                        $arr['company'] = $temp;
                    }
                    else
                    {
                        $data = array();
                        $data['errorcode'] = ErrorCode::AUTH . ErrorCode::UNKNOWN_PWD;
                        return $data;
                    }

                }
                else
                {
                    $data = array();
                    $data['errorcode'] = ErrorCode::AUTH . ErrorCode::UNKNOWN_USRNAME;
                    return $data;
                }
            }
            else
            {
                $data = array();
                $data['errorcode'] = ErrorCode::AUTH . ErrorCode::UNKNOWN_USRNAME;
                return $data;
            }
            return $arr;
        }


    public  function  getlist($cmpid)
    {
        $user_comp = UserInfo::where('company_id','=',$cmpid)
                     ->distinct('user_id')->get();

        $user_ids = array();

        if(count($user_comp))
        {
            foreach($user_comp as $val)
            {
                array_push($user_ids,$val['user_id']);
            }

        }

        $data = array();
        if(count($user_ids))
        {
            $data = User_mgr::where('is_deleted','=','0')
            ->whereIn('id',$user_ids)->get();
        }
        else
        {
            return array();
        }


        $temp = array();
        foreach($data as $val)
        {
            $user_id = $val['id'];
            $arr = array();
            $tem_a = array();
            $arr['id']  =$val['id'];
            $arr['name'] = $val['name'];
            $arr['password'] = Crypt::php_decrypt($val['password']);
            $arr['realname'] = $val['realname'];
            $arr['employee_id'] = $val['employee_id'];
            $arr['department']= $val['department'];
            $arr['status'] = $val['status'];
            $arr['type'] = $val['type'];
            $roledata = User_role::where('user_id','=',$val['id'])
                        ->first();
            $role = Role::where('id','=',$roledata['role_id'])->first();

            if(count($roledata)&&count($role)) {
                $arr['roleid'] = $role['id'];
                $arr['rolename'] = $role['name'];
                $datatmp = Role_function::where('role_id','=',$role['id'])
                                          ->distinct('function_id')->pluck('function_id');
                $arr['function'] = $datatmp;
            }
            else
            {
                $arr['roleid'] = '';
                $arr['rolename'] = '';
                $arr['function'] = '';
            }

            $sql = "select a.company_id,b.company_en_name from user_info a join company_dim b on a.company_id = b.company_id where a.user_id = $user_id  ";
            $data = DB::select($sql);
            $temp_arr = json_decode(json_encode($data), true);
            foreach( $temp_arr as $val)
            {
                $tem_a[$val['company_id']] = $val['company_en_name'];
            }
            $arr['company'] = $tem_a;
            array_push($temp,$arr);

        }

        return $temp;
    }

    public function  resetPassword($uid,Request $request)
    {

        if(!empty($uid))
        {
             $data = User_mgr::where('id','=',$uid)
                ->where('is_deleted','=',0)
                ->first();

            if(count($data))
            {
                $data->password = Crypt::php_encrypt($data['name']);
                $data->save();
                 return array('result'=>'success');
            }
        }
        else
        {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH . ErrorCode::INVALID_PARAM;
            return $data;
        }
    }

    public function getuser($uid)
    {
        if($uid == 0)
        {
            $user_info = User_mgr::where('name','=','admin')->find();
            return $user_info;
        }
        elseif($uid>0)
        {
            $user_info = User_mgr::where('id','=',$uid)->find();
            return $user_info;
        }
        else
        {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH .ErrorCode::INVALID_PARAM;
            return $data;

        }
    }

    public function changepassword($uid,Request $request)
    {

        $oldpwd = $request->input('oldpwd');
        $newpwd = $request->input('newpwd');
        $cfmpwd = $request->input('cfmpwd');


        $data = User_mgr::where('id', '=', $uid)->first();
        if (isset($data) && count($data)) {
            if (Crypt::php_decrypt($data['password']) == trim($oldpwd)) {
                if(trim($newpwd) == trim($cfmpwd))
                {
                    $data->password =Crypt::php_encrypt($newpwd);
                    $data->save();
                    return array('result'=>'success');
                }
                else
                {
                    $data = array();
                    $data['errorcode'] = ErrorCode::AUTH . ErrorCode::UNKNOWN_PWD;
                    return $data;
                }
            }
            else
            {
                $data = array();
                $data['errorcode'] = ErrorCode::AUTH . ErrorCode::UNKNOWN_PWD;
                return $data;
            }
        }
    }

    public  function  deleteuser($uid)
    {
        $data = User_mgr::where('id','=',$uid)->first();

        if(count($data)) {
            $data->is_deleted = 1;

            $data->save();
            return array('result' => 'success');
        }
        else
        {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH.ErrorCode::INVALID_PARAM;
            return $data;
        }
    }

    public  function changestatus($uid,Request $request)
    {

        $status = $request->input('status','');
        $data  = User_mgr::where('id','=',$uid)->first();
        if(count($data)) {
            $data->status = $status;
            $data->save();
        }
        else
        {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH.ErrorCode::INVALID_PARAM;
            return $data;
        }
        $role = User_role::where('user_id','=',$uid)->first();
        if(count($role)) {
            $role->status = $status;
            $role->save();
        }
        $sql = "update user_info set status = $status where user_id = $uid";
        $user_role = DB::update($sql);
        return array('result'=>'success');
    }

    public  function  updateuser($uid,Request $request)
    {
        $name = $request->input('name', '');
        $employee_id = $request->input('empid', '');
        $department = $request->input('dpm', '');
        $realname = $request->input('rname','');
        $status  = $request->input('status','');
        $type = $request->input('type','');
        $user_data = User_mgr::where('name', '=', $name)->first();

        if (isset($user_data) && count($user_data)) {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH . ErrorCode::SAME_USRNAME;
            return $data;
        } else {
            $user_info = User_mgr::where('id', '=', $uid)->first();
            $user_info->name = $name;
            $user_info->employee_id = $employee_id;
            $user_info->department = $department;
            $user_info->realname  =$realname;
            $user_info->status = $status;
            $user_info->$type = $type;
            $user_info->update_date = date("Y-m-d H:i:s",time()+8*3600);
            $user_info->save();

            $data = User_mgr::where('id', '=', $uid)->first();

            return $data;

        }
    }
}
