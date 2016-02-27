<?php

namespace App\Http\Controllers;


use App\Admin;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


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
                if (md5(trim($pwd)) != $adminpwd) {
                    $data = array();
                    $data['errorcode'] = ErrorCode::AUTH . ErrorCode::UNKNOWN_PWD;
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
        $ename = $request->get('sign');
        $oldpwd = $request->input('oldpwd');
        $newpwd = $request->input('newpwd');
        $cfmpwd = $request->input('cfmpwd');
        $crypt = new Crypt();
        $name = $crypt->php_decrypt($ename);
        $data = Admin::where('name', '=', trim($name))->first();
        if (isset($data) && count($data)) {
            if ($data['password'] == trim($oldpwd)) {

            }
        }

    }

    public function changeAdminInfo(Request $request)
    {

    }



}
