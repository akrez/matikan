<?php

namespace app\components;

use Yii;
use yii\base\Component;

class akrez extends Component
{
    public static function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $characters = str_shuffle($characters);
        $characters = substr($characters, 0, $length);
        return $characters;
    }

    public static function normalizeArray($arr, $arrayOut = false)
    {
        if (is_array($arr)) {
            $arr = implode(",", $arr);
        }
        $arr = explode(",", $arr);
        $arr = array_map("trim", $arr);
        $arr = array_unique($arr);
        $arr = array_filter($arr);
        if ($arrayOut) {
            return $arr;
        }
        return implode(",", $arr);
    }
}
