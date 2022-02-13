<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Helper
{
    static protected $dayTH = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
    static protected $monthTH = [null, 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];

    public static function convertToDateTh($date, $slash = "/")
    {
        if (is_null($date)) return "";
        $time = strtotime($date);
        $thdate = date("d", $time);
        $thdate .= $slash . date("m", $time);
        $thdate .= $slash . (date("Y", $time) + 543);
        return $thdate;
    }

    public static function convertToDateMYTh($date, $slash = " ")
    {
        if (is_null($date)) return "";
        $time = strtotime($date);
        $thdate = date("d", $time);
        $thdate .= $slash . self::$monthTH[date("n", $time)];
        $thdate .= $slash . (date("Y", $time) + 543);
        return $thdate;
    }

    public static function convertToDateTimeMYTh($date, $slash = " ")
    {
        if (is_null($date)) return "";
        $time = strtotime(str_replace('/', '-', $date));
        $thdate = date("d", $time);
        $thdate .= $slash . self::$monthTH[date("n", $time)];
        $thdate .= $slash . (date("Y", $time) + 543);
        $thdate .= " " . date("H:i", $time);
        return $thdate;
    }

    public static function convertToDateTimeYTh($date, $slash = "/")
    {
        if (is_null($date)) return "";
        $time = strtotime(str_replace('/', '-', $date));
        $thdate = date("d", $time);
        $thdate .= $slash . date("m", $time);
        $thdate .= $slash . (date("Y", $time) + 543);
        $thdate .= " " . date("H:i", $time);
        return $thdate;
    }


    public static function convertToDateTime($date)
    {
        $time = strtotime($date);
        $thdate = date("d/m/Y H:i", $time);
        return $thdate;
    }

    public static function convertToDate($date)
    {
        $time = strtotime($date);
        $thdate = date("d/m/Y", $time);
        return $thdate;
    }

    public static function parseStringToDate($date)
    {
        return Carbon::parse($date);
    }

    public static function convertStringToDate($date, $format = "d/m/Y")
    {
        return Carbon::createFromFormat($format, $date);
    }

    public static function convertToDateEn($str_date, $slash = "/")
    {
        $arr = explode('/', $str_date);
        return ((int)$arr[2] - 543) . $slash . $arr[1] . $slash . $arr[0];
    }

    public static function __valArr($input, $key)
    {
        return isset($input[$key]) ? $input[$key] : null;
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public static function isEmptyOrNull($str)
    {
        return is_null($str) || Str::of($str)->isEmpty();
    }
    public static function toFloat($number)
    {
        return $number ? (float)filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;
    }
    public static function money_fomat($number, int $decimals = 2, string $decimal_separator = ".", string $thousands_separator = ",")
    {
        return number_format((float) $number, $decimals, $decimal_separator, $thousands_separator);
    }
    public static function valArr($input, $key)
    {
        return isset($input[$key]) ? $input[$key] : null;
    }
    public static function filesize_formatted($size)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}
