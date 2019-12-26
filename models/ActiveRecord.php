<?php

namespace app\models;

use app\components\Jdf;
use yii\db\ActiveRecord as BaseActiveRecord;

class ActiveRecord extends BaseActiveRecord
{

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $now = Jdf::jdate('Y-m-d H:i:s');
        if ($this->isNewRecord) {
            if ($this->hasAttribute('createdAt')) {
                $this->createdAt = $now;
            }
        }
        if ($this->hasAttribute('updatedAt')) {
            $this->updatedAt = $now;
        }
        return true;
    }

    public function attributeLabels()
    {
        return Model::attributeLabelsList();
    }

}
