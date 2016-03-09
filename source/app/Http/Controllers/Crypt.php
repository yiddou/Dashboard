<?php
/**
 * 加密解密类.
 * User: daixi
 * mail: daixi66@163.com
 * Date: 16/2/20
 * Time: 下午7:29
 */
namespace App\Http\Controllers;

class Crypt {

    private static $crypt_key ='Qwert123';


    public static function php_encrypt($str) {
        srand((double)microtime() * 1000000);
        $encrypt_key = md5(rand(0,32000));
        $ctr = 0;
        $tmp = '';
        for($i = 0;$i<strlen($str);$i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $encrypt_key[$ctr].($str[$i]^$encrypt_key[$ctr++]);
        }
        return base64_encode(self::__key($tmp,self::$crypt_key));
    }

    public static function php_decrypt($str) {
        $str = self::__key(base64_decode($str),self::$crypt_key);
        $tmp = '';
        for($i = 0;$i < strlen($str); $i++) {
            $md5 = $str[$i];
            $tmp .= $str[++$i] ^ $md5;
        }
        return $tmp;
    }

    private static function __key($txt,$encrypt_key) {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';
        for($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }

}