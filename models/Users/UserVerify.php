<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_verify}}".
 *
 * @property integer $id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $category
 * @property string $identifier
 * @property string $verify_token
 * @property integer $user_id
 *
 * @property User $user
 */
class UserVerify extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_verify}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'updated_at', 'user_id'], 'integer'],
            [['category'], 'required'],
            [['category'], 'string', 'max' => 24],
            [['identifier', 'verify_token'], 'string', 'max' => 255],
            [['verify_token'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => 'app\models\User', 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'category' => Yii::t('app', 'Category'),
            'identifier' => Yii::t('app', 'Identifier'),
            'verify_token' => Yii::t('app', 'Verify Token'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('app\models\User', ['id' => 'user_id']);
    }
}
