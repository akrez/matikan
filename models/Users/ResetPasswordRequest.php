<?php

namespace app\models\Users;

use app\models\User;
use Yii;

class ResetPasswordRequest extends User
{
    private $_user;

    public function rules()
    {
        return array_merge([
            [['username'], 'required'],
            [['username'], 'usernameValidation'],
        ], parent::rules());
    }

    public function usernameValidation($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findValidUserByUsername($this->username);
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
