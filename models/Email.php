<?php

namespace app\models;

use Yii;

class Email extends Model
{
    const EMAIL_INFO = 'akrez.like@gmail.com';

    private static function send($from, $to, $subject, $view, $params)
    {
        try {
            return \Yii::$app->mailer
                            ->compose($view, $params)
                            ->setFrom($from)
                            ->setTo($to)
                            ->setSubject($subject)
                            ->send();
        } catch (\Exception $e) {
            
        }
        return false;
    }

    public static function resetPasswordRequest($user)
    {
        return self::send(self::EMAIL_INFO, $user->email, 'درخواست تغییر رمزعبور', 'resetPasswordRequest', [
            'name' => $user->name,
            'label' => $user->getAttributeLabel('reset_token'),
            'reset_token' => $user->reset_token,
        ]);
    }
}
