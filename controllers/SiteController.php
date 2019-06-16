<?php

namespace app\controllers;

class SiteController extends Controller
{
    public function behaviors()
    {
        $behaviors = [
            'authenticator' => [],
            'access' => [
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                        'verbs' => ['POST', 'GET'],
                        'roles' => ['?', '@'],
                    ],
                ],
            ]
        ];
        return array_merge_recursive(parent::behaviors(), $behaviors);
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
