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
                        'verbs' => ['POST'],
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['info'],
                        'allow' => true,
                        'verbs' => ['GET'],
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

    public function response($model, $default = [])
    {
        return $default + [
            'status' => !$model->hasErrors(),
            'User' => $model->info(),
            'errors' => $model->errors,
        ];
    }

    public function actionSignup()
    {
        $signup = User::signup(Yii::$app->request->post());
        if ($signup == null) {
            throw new BadRequestHttpException();
        }
        return $this->response($signup);
    }

    public function actionSignin()
    {
        $signin = User::signin(Yii::$app->request->post());
        if ($signin == null) {
            throw new BadRequestHttpException();
        }
        return $this->response($signin, [
                    'User' => ($signin->getUser() ? $signin->getUser()->info(true) : $signin->info()),
        ]);
    }

    public function actionSignout()
    {
        $signout = Yii::$app->user->getIdentity();
        return $this->response($signout, [
                    'status' => $signout && $signout->signout(),
                    'User' => null,
                    'errors' => [],
        ]);
    }

    public function actionResetPasswordRequest()
    {
        $resetPasswordRequest = User::resetPasswordRequest(Yii::$app->request->post());
        if ($resetPasswordRequest == null) {
            throw new BadRequestHttpException();
        }
        return $this->response($resetPasswordRequest);
    }

    public function actionResetPassword()
    {
        $resetPassword = User::resetPassword(Yii::$app->request->post());
        if ($resetPassword == null) {
            throw new BadRequestHttpException();
        }
        return $this->response($resetPassword);
    }

    public function actionProfile()
    {
        $profile = Yii::$app->user->getIdentity();
        if (!$profile) {
            throw new NotFoundHttpException();
        }
        $profile->profile(Yii::$app->request->post());
        if ($profile == null) {
            throw new BadRequestHttpException();
        }
        return $this->response($profile);
    }

    public function actionInfo()
    {
        $profile = Yii::$app->user->getIdentity();
        if (!$profile) {
            throw new NotFoundHttpException();
        }
        return $this->response($profile);
    }

}
