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
            'isbn' => 'ISBN',
            'part' => 'جلد',
            'token' => 'Token',
            'email' => 'ایمیل',
            'title' => 'عنوان',
            'price' => 'قیمت',
            'cover' => 'عکس',
            'status' => 'وضعیت',
            'avatar' => 'عکس',
            'gender' => 'جنسیت',
            'writers' => 'نویسندگان',
            'userId' => 'کاربر',
            'password' => 'رمزعبور',
            'province' => 'استان',
            'username' => 'نام‌کاربری',
            'resetAt' => 'Reset At',
            'birthdate' => 'تاریخ تولد',
            'updatedAt' => 'تاریخ ویرایش',
            'createdAt' => 'تاریخ ایجاد',
            'publishers' => 'انتشارات',
            'resetToken' => 'Reset Token',
            'translators' => 'مترجم',
            'password_hash' => 'Password Hash',
            'publisherYear' => 'سال انتشار',
        ];
    }
}
