<?php

namespace app\models\Users;

use app\models\User;
use Yii;

class Verify extends User
{
    private $_user;

    public function rules()
    {
        return array_merge([
            [['username'], 'required'],
            [['verify_token'], 'required'],
            [['verify_token'], 'verifyTokenValidation'],
        ], parent::rules());
    }

    public function verifyTokenValidation($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findValidUnverifiedByUsernameVerifyToken($this->username, $this->verify_token);
            if ($user) {
                return $this->_user = $user;
            }
            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' =>  $this->getAttributeLabel($attribute)]));
        }
        return $this->_user = null;
    }

    public function getUser()
    {
        return $this->_user;
    }
}
