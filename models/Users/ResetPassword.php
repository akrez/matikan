<?php

namespace app\models\Users;

use app\models\User;
use Yii;

class ResetPassword extends User
{
    private $_user;

    public function rules()
    {
        return array_merge([
            [['username'], 'required'],
            [['password'], 'required'],
            [['reset_token'], 'required'],
            [['reset_token'], 'resetTokenValidation'],
        ], parent::rules());
    }

    public function resetTokenValidation($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findValidUserByUsernameResetToken($this->username, $this->reset_token);
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
