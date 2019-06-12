<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

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
                    [
                        'actions' => ['signup', 'signin', 'reset-password-request', 'reset-password'],
                        'allow' => true,
                        'verbs' => ['POST'],
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['profile'],
                        'allow' => true,
                        'verbs' => ['POST', 'GET'],
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['signout'],
                        'allow' => true,
                        'verbs' => ['GET'],
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
        return array_merge_recursive($behaviors, parent::behaviors());
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionSignup()
    {
        $signup = User::signup(Yii::$app->request->post());
        if ($signup == null) {
            throw new BadRequestHttpException();
        }
        return [
            'status' => !$signup->hasErrors(),
            'User' => $signup->info(),
            'errors' => $signup->errors,
        ];
    }

    public function actionSignin()
    {
        $signin = User::signin(Yii::$app->request->post());
        if ($signin == null) {
            throw new BadRequestHttpException();
        }
        return [
            'status' => !$signin->hasErrors(),
            'User' => $signin->info(),
            'Signin' => ($signin->getUser() ? $signin->getUser()->info(true) : null),
            'errors' => $signin->errors,
        ];
    }

    public function actionSignout()
    {
        $model = Yii::$app->user->getIdentity();
        return [
            'status' => $model && $model->signout(),
        ];
    }

    public function actionResetPasswordRequest()
    {
        $resetPasswordRequest = User::resetPasswordRequest(Yii::$app->request->post());
        if ($resetPasswordRequest == null) {
            throw new BadRequestHttpException();
        }

        return [
            'status' => !$resetPasswordRequest->hasErrors(),
            'User' => $resetPasswordRequest->info(),
            'errors' => $resetPasswordRequest->errors,
        ];
    }

    public function actionResetPassword()
    {
        $resetPassword = User::resetPassword(Yii::$app->request->post());
        if ($resetPassword == null) {
            throw new BadRequestHttpException();
        }

        return [
            'status' => !$resetPassword->hasErrors(),
            'User' => $resetPassword->info(),
            'errors' => $resetPassword->errors,
        ];
    }

    public function actionProfile()
    {
        $profile = Yii::$app->user->getIdentity();
        if (!$profile) {
            throw new NotFoundHttpException();
        }

        if (Yii::$app->request->method == 'POST') {
            $profile->profile(Yii::$app->request->post());
            if ($profile == null) {
                throw new BadRequestHttpException();
            }
        }

        return [
            'status' => !$profile->hasErrors(),
            'User' => $profile->info(),
            'errors' => $profile->errors,
        ];
    }

}
