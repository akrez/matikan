<?php

namespace app\controllers;

use Yii;
use yii\web\Controller as BaseController;
use yii\web\ForbiddenHttpException;

class Controller extends BaseController
{

    const tokenParam = 'token';

    public static function defaultBehaviors($rules = [])
    {
        return [
            'authenticator' => [
                'class' => 'yii\filters\auth\QueryParamAuth',
                'tokenParam' => self::tokenParam,
                'optional' => ['*'],
            ],
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => $rules,
                'denyCallback' => function ($rule, $action) {
                    throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
        ];
    }

}
