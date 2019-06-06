<?php
namespace app\modules\v1\controllers;

use app\controllers\Controller;
use app\models\Status;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\data\Sort;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class PostController extends Controller
{
    public $modelClass = 'app\modules\v1\models\Post';

    public function behaviors()
    {
        $behaviors = [
            'authenticator' => [],
            'access' => [
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'verbs' => ['POST', 'GET', 'DELETE'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
        return array_merge_recursive($behaviors, parent::behaviors());
    }

    public function actionIndex($id = null)
    {
        $method = Yii::$app->request->getMethod();
        if ($id) {
            if ($method == 'GET' || $method == 'POST') {
                return $this->update($id);
            } elseif ($method == 'DELETE') {
                return $this->delete($id);
            }
        } else {
            if ($method == 'GET') {
                return $this->index();
            }
        }
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    public function index()
    {
        $modelClass = (new $this->modelClass);
        $query = $modelClass::find()->where(['AND', ['status' => [Status::STATUS_UNVERIFIED, Status::STATUS_ACTIVE, Status::STATUS_DISABLE]], ['user_id' => Yii::$app->user->getId()]]);

        $sortAttributes = [
            'created_at' => ['label' => \Yii::t('app', 'Created At')],
            'title' => ['label' => \Yii::t('app', 'Title')],
        ];

        $sort = new Sort([
            'defaultOrder' => ['created_at' => SORT_DESC],
            'attributes' => $sortAttributes,
        ]);

        $pagination = new Pagination([
            'defaultPageSize' => 20,
            'pageSizeParam' => 'page_size'
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $sort,
            'pagination' => $pagination,
        ]);

        $models = $dataProvider->getModels();

        $sortAttributeOrders = $dataProvider->getSort()->getAttributeOrders();
        $sortAttribute = key($sortAttributeOrders);
        $sortDirection = (reset($sortAttributeOrders) === SORT_ASC ? 'asc' : 'desc');

        return [
            'Posts' => ArrayHelper::toArray($models),
            'Sort' => [
                'direction' => $sortDirection,
                'attribute' => $sortAttribute,
                'attributes' => $sortAttributes,
            ],
            'Pagination' => [
                'page_count' => $dataProvider->getPagination()->getPageCount(),
                'page_size' => $dataProvider->getPagination()->getPageSize(),
                'page' => $dataProvider->getPagination()->getPage(),
                'total_count' => $dataProvider->getPagination()->totalCount,
            ],
        ];
    }

    public function update($id)
    {
        $model = $this->findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->image = UploadedFile::getInstance($model, 'image');
            $model->save();
        }
        return $model->info();
    }

    public function delete($id)
    {
        $model = $this->findOne($id);
        return ['status' => $model->delete()];
    }

    public function findOne($id)
    {
        $modelClass = (new $this->modelClass);
        $model = $modelClass::find()->where(['AND', ['status' => [Status::STATUS_UNVERIFIED, Status::STATUS_ACTIVE, Status::STATUS_DISABLE]], ['user_id' => Yii::$app->user->getId()]])->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        return $model;
    }
}
