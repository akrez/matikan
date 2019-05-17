<?php
namespace app\models;

use yii\base\Model as BaseModel;

class Model extends BaseModel
{

    public function attributeLabels()
    {
        return static::attributeLabelsList();
    }

    public static function attributeLabelsList()
    {
        return [
            'id' => 'شناسه',
            'name' => 'نام',
            'token' => 'Token',
            'email' => 'ایمیل',
            'status' => 'وضعیت',
            'avatar' => 'عکس',
            'gender' => 'جنسیت',
            'password' => 'رمزعبور',
            'province' => 'استان',
            'username' => 'نام‌کاربری',
            'reset_at' => 'Reset At',
            'birthdate' => 'تاریخ تولد',
            'updated_at' => 'تاریخ ویرایش',
            'created_at' => 'تاریخ ایجاد',
            'reset_token' => 'Reset Token',
            'password_hash' => 'Password Hash',
        ];
    }
}
