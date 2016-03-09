<?php
/**
 * 错误代码类
 * User: daixi
 * mail:daixi66@163.com
 * Date: 16/2/20
 * Time: 下午7:29
 */
namespace App\Http\Controllers;

class ErrorCode
{
    const NAME = 'erorrcode';
    const AUTH = '100';
    const UNKNOWN_USRNAME = '001';
    const UNKNOWN_PWD = '002';
    const UNKNOWN_RESET = '003';
    const EMPTY_NAME_PWD = '004';
    const UNKNOWN_EMAIL = '005';
    const SAME_USRNAME = '006';


    const ERROR  = '001';
    const INVALID_PARAM = '001';

}