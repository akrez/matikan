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
        $inputModel = new User();
        $inputModel->load(Yii::$app->request->post());

        $signup = $inputModel->signup();
        if ($signup == null) {
            throw new BadRequestHttpException();
        }

        return $signup->info();
    }

    public function actionSignin()
    {
        $inputModel = new User();
        $inputModel->load(Yii::$app->request->post());

        $signin = $inputModel->signin();
        if ($signin == null) {
            throw new BadRequestHttpException();
        }

        $user = $signin->getUser();

        if ($signin->hasErrors() || $user == null) {
            return $signin->info();
        }
        return $signin->info() + $user->info(true);
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
        $inputModel = new User();
        $inputModel->load(Yii::$app->request->post());

        $resetPasswordRequest = $inputModel->resetPasswordRequest();
        if ($resetPasswordRequest == null) {
            throw new BadRequestHttpException();
        }

        return [
            'status' => !$resetPasswordRequest->hasErrors(),
            $inputModel->formName() => $resetPasswordRequest->info(['username', 'category']),
        ];
    }

    public function actionResetPassword()
    {
        $inputModel = new User();
        $inputModel->load(Yii::$app->request->post());

        $resetPassword = $inputModel->resetPassword();
        if ($resetPassword == null) {
            throw new BadRequestHttpException();
        }

        return [
            'status' => !$resetPassword->hasErrors(),
            $inputModel->formName() => $resetPassword->info(['username', 'password', 'reset_token']),
        ];
    }

    public function actionProfile()
    {
        $user = Yii::$app->user->getIdentity();
        if (!$user) {
            throw new NotFoundHttpException();
        }

        $user->setScenario('profile');
        if ($user->load(Yii::$app->request->post())) {
            $user->image = UploadedFile::getInstance($user, 'image');
            $user->save();
        }

        return $user->info();
    }

}
