<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/4/2016
 * Time: 2:57 PM
 */

namespace cms\core\utilities;


class Security
{
    var $key;
    function __construct()
    {
        $this->key = 0;
    }
    function generateSalt()
    {
        return time();
    }
    function hash($hashing, $salt)
    {
        return crypt($hashing,"$2y$14$".$salt);
    }
    function encrypt($str)
    {
        $key = $this->key;
        $keylength = strlen($key);
        $strlength = strlen($str);
        $encstring = "";
        for($i=0;$i<=$strlength - 1;$i++) {
            $msgord = ord(substr($str,$i,1));
            $keyord = ord(substr($key,$i % $keylength,1));
            if ($msgord + $keyord <= 255){
                $encstring .= chr($msgord + $keyord);
            }
            if ($msgord + $keyord > 255){
                $encstring .= chr(($msgord + $keyord)-256);
            }
        }
        return $encstring;
    }
    function decrypt($str)
    {
        $key = $this->key;
        $keylength = strlen($key);
        $strlength = strlen($str);
        $decstring = "";
        for($i=0;$i<=$strlength - 1;$i++) {
            $msgord = ord(substr($str,$i,1));
            $keyord = ord(substr($key,$i % $keylength,1));
            if ($msgord - $keyord >= 0){
                $decstring .= chr($msgord - $keyord);
            }
            if ($msgord + $keyord < 0){
                $decstring .= chr(($msgord - $keyord)+256);
            }
        }
        return $decstring;
    }
}