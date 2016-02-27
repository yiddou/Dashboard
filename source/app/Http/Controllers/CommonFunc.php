<?php
namespace App\Http\Controllers;

class CommonFunc
{
   public static function getTheMonth($date)
    {
        $firstDay = date('Ym01', strtotime($date));
        $lastDay = date('Ymd', strtotime("$firstDay +1 month -1 day"));
        return array($firstDay,$lastDay);
    }
}