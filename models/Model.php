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
            'user_id' => 'کاربر',
            'password' => 'رمزعبور',
            'province' => 'استان',
            'username' => 'نام‌کاربری',
            'reset_at' => 'Reset At',
            'birthdate' => 'تاریخ تولد',
            'updated_at' => 'تاریخ ویرایش',
            'created_at' => 'تاریخ ایجاد',
            'publishers' => 'انتشارات',
            'reset_token' => 'Reset Token',
            'translators' => 'مترجم',
            'password_hash' => 'Password Hash',
            'publisher_year' => 'سال انتشار',
        ];
    }
}
