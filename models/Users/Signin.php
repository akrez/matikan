<?php

namespace app\models\Users;

use app\models\User;
use Yii;

class Signin extends User
{
    private $_user;

    public function rules()
    {
        return array_merge([
            [['email'], 'required'],
            [['password'], 'required'],
            [['password'], 'passwordValidation'],
        ], parent::rules());
    }

    public function passwordValidation($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findValidUserByEmail($this->email);
            if ($user && $user->validatePassword($this->password)) {
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
