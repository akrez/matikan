<?php

namespace app\models;

use app\components\Helper;
use app\components\jdf;
use app\models\Users\ResetPassword;
use app\models\Users\ResetPasswordRequest;
use app\models\Users\Signin;
use app\models\Users\Signup;
use Exception;
use Yii;
use yii\imagine\Image;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $updated_at
 * @property string $created_at
 * @property string $status
 * @property string $birthdate
 * @property string $province
 * @property string $token
 * @property string $password_hash
 * @property string $username
 * @property string $email
 * @property string $avatar
 * @property string $gender
 * @property string $name
 * @property string $reset_at
 * @property string $reset_token
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $password;
    public $image;

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function rules()
    {
        return [
                [['email'], 'email'],
                [['username'], 'maxLenValidation', 'params' => ['max' => 16]],
                [['password', 'username'], 'minLenValidation', 'params' => ['min' => 6]],
                [['birthdate'], function ($attribute, $params, $validator) {
                    $jdate = explode('-', $this->$attribute);
                    if (count($jdate) == 3 && jdf::jcheckdate($jdate[1], $jdate[2], $jdate[0])) {
                    } else {
                        $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
                    }
                }, 'on' => ['profile']],
                [['name'], 'match', 'pattern' => "/^[\x{0600}-\x{06FF} a-z A-Z]{3,31}$/u", 'on' => ['profile']],
                [['gender'], 'in', 'range' => array_keys(Gender::getList()), 'on' => ['profile']],
                [['province'], 'in', 'range' => array_keys(Province::getList()), 'on' => ['profile']],
                [['image'], 'file', 'extensions' => ['jpg', 'png'], 'maxSize' => 1024 * 1024, 'mimeTypes' => ['image/jpeg', 'image/png'], 'on' => ['profile']],
                [['image'], 'uploadAvatarValidation', 'skipOnError' => true, 'on' => ['profile']],
        ];
    }

    /////

    public static function findIdentity($id)
    {
        return static::find()->where(['id' => $id])->andWhere(['status' => [Status::STATUS_UNVERIFIED, Status::STATUS_ACTIVE, Status::STATUS_DISABLE]])->one();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->where(['token' => $token])->andWhere(['status' => [Status::STATUS_UNVERIFIED, Status::STATUS_ACTIVE, Status::STATUS_DISABLE]])->one();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->token;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /////

    public function uploadAvatarValidation($attribute, $params, $validator)
    {
        try {
            $ext = explode('/', $this->image->type);
            $desFile = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . 'gallery' . DIRECTORY_SEPARATOR . 'avatar';
            do {
                $name = substr(uniqid(rand(), true), 0, 11) . '.' . $ext[1];
            } while (file_exists($desFile . DIRECTORY_SEPARATOR . $name));
            $desFile = $desFile . DIRECTORY_SEPARATOR . $name;
            $image = Image::getImagine()->open($this->image->tempName);
            Image::resize($image, 300, 300, false, true)->save($desFile, ['quality' => 100]);
            $this->avatar = $name;
        } catch (Exception $ex) {
            $this->avatar = null;
        }
    }

    public function minLenValidation($attribute, $params, $validator)
    {
        $min = $params['min'];
        if (strlen($this->$attribute) < $min) {
            $this->addError($attribute, Yii::t('yii', '{attribute} must be no less than {min}.', ['min' => $min, 'attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

    public function maxLenValidation($attribute, $params, $validator)
    {
        $max = $params['max'];
        if ($max < strlen($this->$attribute)) {
            $this->addError($attribute, Yii::t('yii', '{attribute} must be no greater than {max}.', ['max' => $max, 'attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

    public function setPasswordHash($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function setAuthKey()
    {
        return $this->token = Yii::$app->security->generateRandomString();
    }

    public static function findValidUserByEmail($email)
    {
        return self::find()->where(['status' => [Status::STATUS_UNVERIFIED, Status::STATUS_ACTIVE, Status::STATUS_DISABLE]])->andWhere(['email' => $email])->one();
    }

    public static function findValidUserByEmailResetToken($email, $resetToken)
    {
        return self::find()->where(['status' => [Status::STATUS_UNVERIFIED, self::STATUS_ACTIVE, Status::STATUS_DISABLE]])->andWhere(['email' => $email])->andWhere(['reset_token' => $resetToken])->andWhere(['>', 'reset_at', time() - self::TIMEOUT])->one();
    }

    public function generateResetToken()
    {
        do {
            $rand = rand(10000, 99999);
            $model = self::find()->where(['reset_token' => $rand])->one();
        } while ($model != null);
        return $rand;
    }

    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /////

    public function info($includeToken = false)
    {
        $attributes = $this->attributes + ['password' => $this->password];
        $errors = $this->errors;

        $fields = ['id' => null, 'updated_at' => null, 'created_at' => null, 'status' => null, 'username' => null, 'email' => null, 'name' => null, 'province' => null, 'birthdate' => null, 'avatar' => null, 'gender' => null];
        if ($includeToken) {
            $fields['token'] = null;
        }

        return[
            $this->formName() => [
                'status' => empty($errors),
                'attributes' => array_intersect_key($attributes, $fields),
                'errors' => $errors,
            ]
        ];
    }

    public function signup($status = Status::STATUS_UNVERIFIED)
    {
        try {
            $signup = new Signup();
            $signup->password = $this->password;
            $signup->email = $this->email;
            $signup->username = Helper::generateRandomString(8);
            $signup->status = $status;
            $signup->setAuthKey();
            $signup->setPasswordHash($this->password);
            $signup->save();
            return $signup;
        } catch (Exception $e) {
            dd($e->getMessage());
            return null;
        }
    }

    public function signin()
    {
        try {
            $signin = new Signin();
            $signin->email = $this->email;
            $signin->password = $this->password;

            $signin->validate();

            return $signin;
        } catch (Exception $e) {
            return null;
        }
    }

    public function signout()
    {
        try {
            $this->setAuthKey();
            return $this->save();
        } catch (Exception $e) {
            return null;
        }
    }

    public function resetPasswordRequest()
    {
        try {
            $resetPasswordRequest = new ResetPasswordRequest();
            $resetPasswordRequest->username = $this->username;
            if ($resetPasswordRequest->validate()) {
                $user = $resetPasswordRequest->getUser();
                $user->reset_token = self::generateResetToken();
                $user->reset_at = time();
                if ($user->save()) {
                    Email::resetPasswordRequest($user->email, $user);
                    return $resetPasswordRequest;
                }
                return null;
            }
            return $resetPasswordRequest;
        } catch (Exception $e) {
            return null;
        }
    }

    public function resetPassword()
    {
        try {
            $resetPassword = new ResetPassword();
            $resetPassword->username = $this->username;
            $resetPassword->password = $this->password;
            $resetPassword->reset_token = $this->reset_token;
            if ($resetPassword->validate()) {
                $user = $resetPassword->getUser();
                $user->reset_token = null;
                $user->reset_at = null;
                $user->setPasswordHash($this->password);
                if ($user->save()) {
                    return $resetPassword;
                }
                return null;
            }
            return $resetPassword;
        } catch (Exception $e) {
            return null;
        }
    }
}
