<?php

namespace App\Http\Controllers;

use App\Func;
use App\Role;
use App\Role_function;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    //

    public  function  addrole(Request $request)
    {
        $name = $request->input('name','');
        $description = $request->input('desc','');
        $funtioid = $request->input('funcid','');

        if(!empty($name))
        {
            $role = new Role();
            $role->name = $name;
            $role->description = $description;
            $role->update_date = date("Y-m-d H:i:s",time()+8*3600);
            $role->is_deleted = 0;
            $role->save();
            $role_id = $role->id;
            $arr = explode('_',$funtioid);
            foreach($arr as $val)
            {
                $role_func = new Role_function();
                $role_func->role_id = $role_id;
                $role_func->function_id = $val;
                $role_func->save();
                return array('result' => 'success');
            }
        }
        else
        {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH.ErrorCode::INVALID_PARAM;
            return $data;
        }

    }

    public function updaterole($roleid,Request $request)
    {
        $name = $request->input('name','');
        $description = $request->input('desc','');
        $funtioid = $request->input('funcid','');


        if(!empty($name))
        {
            $role_data  = Role::where('id','=',$roleid)->first();
            $role_data->name = $name;
            $role_data->description = $description;
            $role_data->update_date = date("Y-m-d H:i:s",time()+8*3600);
            $role_data->save();

            $sql = "delete from role_function where role_id = $roleid";
            DB::delete($sql);

            $arr = explode('_',$funtioid);
            foreach($arr as $val)
            {
                $role_func = new Role_function();
                $role_func->role_id = $roleid;
                $role_func->function_id = $val;
                $role_func->save();
            }
            return array('result' => 'success');
        }
        else
        {
            $data = array();
            $data['errorcode'] = ErrorCode::AUTH.ErrorCode::INVALID_PARAM;
            return $data;
        }


    }

    public  function getroles()
    {
        $data = Role::where('is_deleted','=',0)->get();
        $data_a  = array();
        foreach($data as $val)
        {
            $arr = array();
            $roleid = $val['id'];
            $arr['id']  =$val['id'];
            $arr['name'] =$val['name'];
            $arr['description'] = $val['description'];
            $arr['insert_date']  =$val['insert_date'];
            $sql =  "select a.id,a.name,a.description from function a join role_function b on a.id = b.function_id where b.role_id = $roleid ";
            $data_arr = DB::select($sql);
            $temp = json_decode(json_encode( $data_arr), true);
            $arr['function'] = $temp;
            array_push($data_a,$arr);

        }
        return $data_a;
    }

    public  function  deleterole($roleid)
    {
        if($roleid>0) {
            $sql = "update role set is_deleted = 1 where id = $roleid";
            DB::update($sql);
            $delete_fun = "delete from role_function where role_id = $roleid";
            DB::delete($delete_fun);
            $delete_user = "delete from user_role where role_id = $roleid";
            DB::delete($delete_user);
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
