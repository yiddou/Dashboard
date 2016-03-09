<?php

namespace App\Http\Controllers;



use App\Admin;
use App\Company_dim;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function  login(Request $request)
    {
        $name = $request->input('name', '');
        $pwd = $request->input('pwd', '');
        if (!empty($name) && !empty($pwd)) {
            $data = Admin::where('name', '=', trim($name))->first();
            if (isset($data) && count($data)) {
                $adminpwd = $data['password'];
                if (trim($pwd) != Crypt::php_decrypt($adminpwd)) {
                    $data = array();
                    $data['errorcode'] = ErrorCode::AUTH . ErrorCode::UNKNOWN_PWD;
                }
                else
                {
                    $data = array();
                    $data['user_id'] = 0;
                    $data['name'] = 'super admin';
                    $data['role'] = 0;
                    $data['role_name'] = 'super_admin';
                    $data['company'] = Company_dim::all()->pluck('company_en_name','company_id');
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

    public function confirmMail(Request $request)
    {
        $result = array();
        $ename = $request->get('sign');
        $email = $request->input('email');
        $crypt = new Crypt();
        $name = $crypt->php_decrypt($ename);
        $data = Admin::where('name', '=', trim($name))->first();
        if ($data['email'] == trim($email)) {
            $result['result'] = 'success';
        } else {
            $result['errorcode'] = ErrorCode::AUTH . ErrorCode::UNKNOWN_EMAIL;

        }
        return $result;
    }


    public function resetPassword(Request $request)
    {

        $oldpwd = $request->input('oldpwd');
        $newpwd = $request->input('newpwd');
        $cfmpwd = $request->input('cfmpwd');


        $data = Admin::where('name', '=', 'admin')->first();
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


    }

    public function changeAdminInfo(Request $request)
    {
        $name = $request->input('name','');
        $email = $request->input('email','');

        $data = Admin::where('name', '=', 'admin')->first();

        $data->email = $email;
        $data->save();
        return array('result'=>'success');
    }

    public function fogetpassword()
    {
        $data = Admin::where('name', '=', 'admin')->first();
        $email = $data['email'];
        $name = $data['name'];

        $data = ['email'=>$email, 'name'=>$name];
        Mail::send('welcome', $data, function($message) use($data)
        {
            $message->to($data['email'], $data['name'])->subject('admin密码找回');
        });
    }



}
