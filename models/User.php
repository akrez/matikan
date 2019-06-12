<?php

namespace app\models;

use app\components\Helper;
use app\components\jdf;
use app\models\Email;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
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

    const TIMEOUT_RESET = 120;

    public $password;
    public $image;
    public $_user;

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function rules()
    {
        $scenariosRules = [
            'signup' => [
                'email' => [['required'], ['unique'],],
                '!username' => [['required'], ['unique'],],
                'password' => [['required'],],
            ],
            'signin' => [
                'email' => [['required']],
                'password' => [['required'], ['passwordValidation']],
            ],
            'resetPasswordRequest' => [
                'email' => [['required'], ['findValidUserByUsername']],
            ],
            'resetPassword' => [
                'email' => [['required'], ['findValidUserByUsername']],
                'password' => [['required']],
                'reset_token' => [['required']],
            ],
            'profile' => [
                'birthdate' => [],
                'name' => [],
                'gender' => [],
                'province' => [],
                'image' => [],
            ]
        ];
        $attributesRules = [
            'reset_token' => [
            ],
            'email' => [
                ['email'],
            ],
            'username' => [
                ['minLenValidation', 'params' => ['min' => 6]],
                ['maxLenValidation', 'params' => ['max' => 16]],
            ],
            'password' => [
                ['minLenValidation', 'params' => ['min' => 6]],
            ],
            'birthdate' => [
                ['birthdateValidation'],
            ],
            'name' => [
                ['match', 'pattern' => "/^[\x{0600}-\x{06FF} a-z A-Z]{3,31}$/u"],
            ],
            'gender' => [
                ['in', 'range' => array_keys(Gender::getList())],
            ],
            'province' => [
                ['in', 'range' => array_keys(Province::getList())],
            ],
            'image' => [
                ['file', 'extensions' => ['jpg', 'png'], 'maxSize' => 1024 * 1024, 'mimeTypes' => ['image/jpeg', 'image/png']],
                ['uploadAvatarValidation', 'skipOnError' => true],
            ],
        ];

        $rules = [];
        foreach ($scenariosRules as $scenario => $scenarioAttributes) {
            foreach ($scenarioAttributes as $attributeLabel => $scenarioRules) {
                $attribute = ($attributeLabel[0] == '!' ? substr($attributeLabel, 1) : $attributeLabel);
                foreach ($scenarioRules as $scenarioRule) {
                    $rules[] = array_merge([[$attributeLabel]], $scenarioRule, ['on' => $scenario]);
                }
                foreach ($attributesRules[$attribute] as $attributeRule) {
                    $rules[] = array_merge([[$attributeLabel]], $attributeRule, ['on' => $scenario]);
                }
            }
        }

        return $rules;
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

    public function passwordValidation($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findValidUserByEmail($this->email);
            if ($user && $user->validatePassword($this->password)) {
                return $this->_user = $user;
            }
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }
        return $this->_user = null;
    }

    public function findValidUserByUsername($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findValidUserByEmail($this->email);
            if ($user) {
                return $this->_user = $user;
            }
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }
        return $this->_user = null;
    }

    public function birthdateValidation($attribute, $params, $validator)
    {
        $jdate = explode('-', $this->$attribute);
        if (count($jdate) == 3 && jdf::jcheckdate($jdate[1], $jdate[2], $jdate[0])) {
            
        } else {
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

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

    public function setResetToken()
    {
        if (empty($this->reset_token) || time() - self::TIMEOUT_RESET > $this->reset_at) {
            $this->reset_token = self::generateResetToken();
        }
        $this->reset_at = time();
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
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function getUser()
    {
        return $this->_user;
    }

    /////

    public function info($includeToken = false)
    {
        return [
            'id' => $this->id,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'name' => $this->name,
            'province' => $this->province,
            'birthdate' => $this->birthdate,
            'avatar' => $this->avatar,
            'gender' => $this->gender,
            'token' => ($includeToken ? $this->token : null),
        ];
    }

    public static function signup($input)
    {
        try {
            $signup = new User(['scenario' => 'signup']);
            $signup->load($input, '');
            $signup->username = Helper::generateRandomString(8);
            $signup->status = Status::STATUS_UNVERIFIED;
            $signup->setAuthKey();
            $signup->setPasswordHash($signup->password);
            $signup->save();
            return $signup;
        } catch (Exception $e) {
            return null;
        }
    }

    public function profile($input)
    {
        try {
            $this->setScenario('profile');
            $this->load($input, '');
            $this->image = UploadedFile::getInstanceByName('image');
            $this->save();
            return $this;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function signin($input)
    {
        try {
            $signin = new User(['scenario' => 'signin']);
            $signin->load($input, '');
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

    public static function resetPasswordRequest($input)
    {
        try {
            $resetPasswordRequest = new User(['scenario' => 'resetPasswordRequest']);
            $resetPasswordRequest->load($input, '');
            if ($resetPasswordRequest->validate()) {
                $user = $resetPasswordRequest->getUser();
                $user->setResetToken();
                if ($user->save(false)) {
                    Email::resetPasswordRequest($user);
                } else {
                    return null;
                }
            }
            return $resetPasswordRequest;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function resetPassword($input)
    {
        try {
            $resetPassword = new User(['scenario' => 'resetPassword']);
            $resetPassword->load($input, '');
            if ($resetPassword->validate()) {
                $user = $resetPassword->getUser();
                $user->reset_token = null;
                $user->reset_at = null;
                $user->status = Status::STATUS_ACTIVE;
                $user->setPasswordHash($resetPassword->password);
                if ($user->save(false)) {
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
