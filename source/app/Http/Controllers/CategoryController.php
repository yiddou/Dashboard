<?php

namespace App\Http\Controllers;

use App\Category_dim;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    //
    public function getCategory($id)
    {
        $data  = Category_dim::where('parent_category_id','=',$id)->distinct('category_id')->pluck('name','category_id');
        return $data;
    }
}
