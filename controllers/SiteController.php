<?php

namespace app\controllers;

class SiteController extends Controller
{

    public function behaviors()
    {
        return self::defaultBehaviors([
            [
                'actions' => ['error'],
                'allow' => true,
                'verbs' => ['POST', 'GET'],
                'roles' => ['?', '@'],
            ],
        ]);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

}
