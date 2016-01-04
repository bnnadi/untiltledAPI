<?php
/**
 * Created by PhpStorm.
 * User: Bisike Nnadi
 * Date: 1/4/2016
 * Time: 3:33 PM
 */

namespace cms\core\utilities;

//TODO: Should I even use this? What would it be used for?

class Location
{
    var $street_address;
    var $long;
    var $lat;
    var $ip_address;
    var $headers = array();
    var $error_message;

    private function getData($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if(count($this->headers) > 0)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data);
    }
}