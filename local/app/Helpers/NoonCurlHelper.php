<?php

namespace App\Helpers;

class NoonCurlHelper
{
    public static function post($url, $fields, $header)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 1);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $result = curl_error($ch);
            \Log::error($result);
            return $result;
        }

        curl_close($ch);
        return $result;
    }

    public static function get($url, $header)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 1);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $result = curl_error($ch);
            \Log::error($result);
            return $result;
        }
        curl_close($ch);
        return $result;
    }
}
