<?php

namespace app\models\Users;

class Signup extends Profile
{

    public function rules()
    {
        return array_merge([
            [['username', 'email', 'password'], 'required'],
            [['username', 'email'], 'unique'],
        ], parent::rules());
    }

}
