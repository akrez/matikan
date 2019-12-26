<?php

namespace app\models;

use Yii;

class Status extends Model
{

    const STATUS_UNVERIFIED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLE = 2;
    const STATUS_BLOCKED = 3;
    const STATUS_DELETED = 4;

    public static function getList()
    {
        return [
            self::STATUS_UNVERIFIED => Yii::t('app', 'Unverified'),
            self::STATUS_ACTIVE => Yii::t('app', 'Active'),
            self::STATUS_DISABLE => Yii::t('app', 'Disable'),
            self::STATUS_BLOCKED => Yii::t('app', 'Blocked'),
            self::STATUS_DELETED => Yii::t('app', 'Deleted'),
        ];
    }

    public static function getLabel($item)
    {
        $list = self::statuses();
        return (isset($list[$item]) ? $list[$item] : null);
    }

    public static function getValidStatuses()
    {
        return [self::STATUS_DISABLE, self::STATUS_ACTIVE];
    }

}
