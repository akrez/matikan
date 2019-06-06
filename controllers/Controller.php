<?php
namespace app\controllers;

use Yii;
use yii\web\Controller as BaseController;
use yii\web\ForbiddenHttpException;

class Controller extends BaseController
{
    const tokenParam = 'token';

    public function behaviors()
    {
        $behaviors = [
            'authenticator' => [
                'class' => 'yii\filters\auth\QueryParamAuth',
                'tokenParam' => self::tokenParam,
                'optional' => ['*'],
            ],
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'denyCallback' => function ($rule, $action) {
                    throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
        ];
        return array_merge_recursive(parent::behaviors(), $behaviors);
    }
}
