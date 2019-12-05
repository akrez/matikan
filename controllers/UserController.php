<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{

    public function behaviors()
    {
        return self::defaultBehaviors([
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
                'actions' => ['info', 'signout'],
                'allow' => true,
                'verbs' => ['GET'],
                'roles' => ['@'],
            ],
        ]);
    }

    public function response($model, $includeToken = false)
    {
        return [
            'status' => !$model->hasErrors(),
            'user' => [
                'id' => $model->id,
                'updatedAt' => $model->updatedAt,
                'createdAt' => $model->createdAt,
                'status' => $model->status,
                'username' => $model->username,
                'email' => $model->email,
                'password' => null,
                'resetToken' => null,
                'name' => $model->name,
                'province' => $model->province,
                'birthdate' => $model->birthdate,
                'avatar' => $model->avatar,
                'gender' => $model->gender,
                'token' => ($includeToken ? $model->token : null),
            ],
            'errors' => $model->errors,
        ];
    }

    public function actionSignin()
    {
        $signin = User::signin(Yii::$app->request->post());
        if ($signin == null) {
            throw new BadRequestHttpException();
        }
        if ($user = $signin->getUser()) {
            return $this->response($user, true);
        }
        return $this->response($signin);
    }

    public function actionSignout()
    {
        $signout = Yii::$app->user->getIdentity();
        if (!$signout) {
            throw new NotFoundHttpException();
        }
        $signout = $signout->signout();
        if ($signout == null) {
            throw new BadRequestHttpException();
        }
        return $this->response($signout);
    }

    public function actionSignup()
    {
        $signup = User::signup(Yii::$app->request->post());
        if ($signup == null) {
            throw new BadRequestHttpException();
        }
        return $this->response($signup);
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
        $profile = $profile->profile(Yii::$app->request->post());
        if ($profile == null) {
            throw new BadRequestHttpException();
        }
        return $this->response($profile);
    }

    public function actionInfo()
    {
        $info = Yii::$app->user->getIdentity();
        if (!$info) {
            throw new NotFoundHttpException();
        }
        return $this->response($info);
    }

}
