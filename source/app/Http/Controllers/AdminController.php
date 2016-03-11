<?php

namespace App\Http\Controllers;



use App\Admin;
use App\Company_dim;
use App\Func;
use App\Role_function;
use App\User_mgr;
use App\User_role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function  login(Request $request)
    {
        $name = $request->input('name', '');
        $pwd = $request->input('pwd', '');
        if (!empty($name) && !empty($pwd)) {

            $data = User_mgr::where('name','=',trim($name))
                    ->where('type','!=',3)
                    ->where('status','=',1)
                    ->where('is_deleted','=',0)
                    ->first();
            if (isset($data) && count($data)) {
                $adminpwd = $data['password'];
                if (trim($pwd) != Crypt::php_decrypt($adminpwd)) {
                    $data = array();
                    $data['errorcode'] = ErrorCode::AUTH . ErrorCode::UNKNOWN_PWD;
                }
                else
                {
                    if($data['name'] == 'admin') {
                        $arr = array();
                        $arr['user_id'] = 0;
                        $arr['name'] = $data['name'];
                        $arr['realname'] = $data['realname'];
                        $arr['function'] = Func::all()->pluck('id');
                        $arr['role'] = 0;
                        $arr['role_name'] = 'super_admin';
                        $arr['type'] = $data['type'];
                        $arr['company'] = Company_dim::all()->pluck('company_en_name', 'company_id');

                        return $arr;
                    }
                    else
                    {
                        $arr = array();
                        $arr['id'] = $data['id'];
                        $arr['name'] = $data['name'];
                        $arr['realname'] = $data['rname'];
                        $arr['employee_id'] = $data['employee_id'];
                        $arr['department']  = $data['department'];
                        $arr['email']  = $data['email'];
                        $arr['type']  = $data['type'];
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

                        return $arr;
                    }
                }
            } else {
                $data = array();
                $data['errorcode'] = ErrorCode::AUTH . ErrorCode::UNKNOWN_USRNAME;
            }
            return $data;
        } else {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH . ErrorCode::EMPTY_NAME_PWD;
            return $data;
        }
    }


    public function changepassword($uid,Request $request)
    {

        $oldpwd = $request->input('oldpwd');
        $newpwd = $request->input('newpwd');
        $cfmpwd = $request->input('cfmpwd');

        $data = array();
        if($uid == 0)
        {
            $data = User_mgr::where('name', '=', 'admin')->first();
        }
        else
        {
            $data = User_mgr::where('id', '=', $uid)->first();
        }

        if (isset($data) && count($data)) {
            if (Crypt::php_decrypt($data['password']) == trim($oldpwd)) {
                if(trim($newpwd) == trim($cfmpwd))
                {
                    $data->password = Crypt::php_encrypt($newpwd);
                    $data->save();
                    return array('result'=>'success');
                }
            }
            else
            {
                $data = array();
                $data['errorcode'] = ErrorCode::AUTH.ErrorCode::UNKNOWN_PWD;
                return $data;
            }
        }
        else
        {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH.ErrorCode::INVALID_PARAM;
            return $data;
        }


    }

    public function changeAdminInfo($uid,Request $request)
    {
        $name = $request->input('name','');
        $email = $request->input('email','');
        $employee_id = $request->input('empid', '');
        $department = $request->input('dpm', '');
        $realname = $request->input('rname','');

        if($uid == 0) {
            $data = User_mgr::where('name', '=', 'admin')->first();
            $data->employee_id = $employee_id;
            $data->department =  $department;
            $data->email = $email;
            $data->realname = $realname;
            $data->update_date = date('Y-m-d h:i:s',time()+8*3600);
            $data->save();
            return array('result' => 'success');
        }
        elseif($uid >0)
        {
            $data = User_mgr::where('id', '=', $uid)->first();
            $data->employee_id = $employee_id;
            $data->department =  $department;
            $data->email = $email;
            $data->realname = $realname;
            $data->update_date = date('Y-m-d h:i:s',time()+8*3600);
            return array('result' => 'success');
        }
        else
        {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH.ErrorCode::INVALID_PARAM;
            return $data;
        }
    }

    public function fogetpassword(Request $request)
    {
        $email_data = $request->input('email','');
        $data = User_mgr::where('name', '=', 'admin')->first();
        $email = $data['email'];
        $name = $data['name'];
        $password = $data['password'];

        if(isset($email) && isset($name)&&isset($password)&&$email_data == $email) {
            $data = ['email' => $email, 'name' => $name,'password'=>Crypt::php_decrypt(trim($password))];
            Mail::send('password', $data, function ($message) use ($data) {
                $message->to($data['email'], $data['name'])->subject('admin密码找回');
            });
            return array('result' => 'success');
        }
        else
        {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH.ErrorCode::INVALID_PARAM;
            return $data;
        }
    }



}
