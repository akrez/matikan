<?php
namespace app\modules\v1\models;

use app\components\Helper;
use app\components\jdf;
use app\models\ActiveRecord;
use app\models\Model;
use app\models\Province;
use app\models\Status;
use app\models\User;
use Yii;
use yii\httpclient\Exception;
use yii\imagine\Image;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $updated_at
 * @property string $created_at
 * @property string $status
 * @property string $title
 * @property string $isbn
 * @property int $price
 * @property int $province
 * @property string $publishers
 * @property string $writers
 * @property string $translators
 * @property int $publisher_year
 * @property int $part
 * @property string $cover
 * @property int $user_id
 *
 * @property User $user
 */
class Post extends ActiveRecord
{
    public $image;

    public static function tableName()
    {
        return 'post';
    }

    public function rules()
    {
        return [
            [['status', 'title', 'isbn', 'price', 'province', '!user_id'], 'required'],
            [['price'], 'integer', 'min' => -1],
            [['part'], 'integer', 'min' => 1],
            [['publisher_year'], 'integer', 'min' => 1357, 'max' => jdf::jdate('Y')],
            [['status'], 'in', 'range' => [Status::STATUS_UNVERIFIED, Status::STATUS_ACTIVE, Status::STATUS_DISABLE]],
            [['title', 'publishers', 'writers', 'translators'], 'string', 'max' => 512],
            [['isbn'], 'match', 'pattern' => "/^[0-9]{1,1}[0-9x\-]{8,11}[0-9]{1,1}$/u"],
            [['province'], 'in', 'range' => array_keys(Province::getList())],
            [['image'], 'file', 'extensions' => ['jpg', 'png'], 'maxSize' => 1024 * 1024, 'mimeTypes' => ['image/jpeg', 'image/png']],
            [['image'], 'uploadCoverValidation', 'skipOnError' => true],
        ];
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        $this->publishers = Helper::normalizeArray($this->publishers);
        $this->writers = Helper::normalizeArray($this->writers);
        $this->translators = Helper::normalizeArray($this->translators);
        $this->user_id = Yii::$app->user->getId();
        return true;
    }

    public function uploadCoverValidation($attribute, $params, $validator)
    {
        try {
            $imageSize = getimagesize($this->image->tempName);
            if ($imageSize[0] > $imageSize[1] && (3 < $imageSize[0] / $imageSize[1])) {
                return $this->addError($attribute, 'نسبت عرض به طول عکس نباید بیشتر از 3 برابر باشد.');
            } elseif ($imageSize[1] > $imageSize[0] && (3 < $imageSize[1] / $imageSize[0])) {
                return $this->addError($attribute, 'نسبت طول به عرض عکس نباید بیشتر از 3 برابر باشد.');
            }

            $ext = explode('/', $this->image->type);
            $desFile = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . 'gallery' . DIRECTORY_SEPARATOR . 'cover';
            do {
                $name = substr(uniqid(rand(), true), 0, 11) . '.' . $ext[1];
            } while (file_exists($desFile . DIRECTORY_SEPARATOR . $name));
            $desFile = $desFile . DIRECTORY_SEPARATOR . $name;
            $image = Image::getImagine()->open($this->image->tempName);
            Image::resize($image, 300, 300, false, true)->save($desFile, ['quality' => 100]);
            $this->cover = $name;
        } catch (Exception $ex) {
            $this->cover = null;
        }
    }

    public function info(array $fields = [], array $expand = [], $recursive = true)
    {
        $errors = $this->errors;

        return [
            $this->formName() => [
                'status' => empty($errors),
                'attributes' => parent::toArray($fields, $expand, $recursive),
                'errors' => $errors,
            ],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
