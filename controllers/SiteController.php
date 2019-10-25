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
                    ],
        ]);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => (YII_DEBUG ? '@yii/views/errorHandler/exception.php' : '@yii/views/errorHandler/error.php'),
            ],
        ];
    }

    }
