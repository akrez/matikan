<?php

namespace app\models;

class Gender extends Model
{

    const GENDER_MALE = 'm';
    const GENDER_FEMALE = 'f';

    public static function getList()
    {
        return [
            self::GENDER_MALE => 'مرد',
            self::GENDER_FEMALE => 'زن',
        ];
    }

    public static function getLabel($item)
    {
        $list = self::statuses();
        return (isset($list[$item]) ? $list[$item] : null);
    }

}
