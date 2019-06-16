<?php

namespace app\components;

use Yii;
use yii\base\Component;

class Helper extends Component
{
    public static function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString = $randomString . $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function normalizeArray($arr, $arrayOut = false)
    {
        if (is_array($arr)) {
            $arr = implode(",", $arr);
        }
        $arr = str_ireplace("\n", ",", $arr);
        $arr = str_ireplace(",", ",", $arr);
        $arr = str_ireplace("،", ",", $arr);
        $arr = explode(",", $arr);
        $arr = array_map("trim", $arr);
        $arr = array_unique($arr);
        $arr = array_filter($arr);
        sort($arr);
        if ($arrayOut) {
            return $arr;
        }
        return implode(",", $arr);
    }
}
