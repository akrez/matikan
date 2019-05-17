<?php

namespace app\models;

use app\components\jdf;
use yii\db\ActiveRecord as BaseActiveRecord;

class ActiveRecord extends BaseActiveRecord
{

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $now = date('Y-m-d H:i:s');
        if ($this->isNewRecord) {
            if ($this->hasAttribute('created_at')) {
                $this->created_at = $now;
            }
        }
        if ($this->hasAttribute('updated_at')) {
            $this->updated_at = $now;
        }
        return true;
    }

    public function attributeLabels()
    {
        return Model::attributeLabelsList();
    }

}
