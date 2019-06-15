<?php
namespace app\modules\v1\controllers;

use app\controllers\Controller;
use app\models\Status;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use app\components\SingleSort;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use app\modules\v1\models\Post;

class PostController extends Controller
{
    public $modelClass = 'app\modules\v1\models\Post';

    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'verbs' => ['GET'],
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['create', 'update'],
                        'allow' => true,
                        'verbs' => ['POST'],
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'verbs' => ['DELETE'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
        return array_merge_recursive(parent::behaviors(), $behaviors);
    }

    public function actionIndex()
    {
        $page = Yii::$app->request->get('page');
        $sort = Yii::$app->request->get('sort');

        $query = (new $this->modelClass)::find()->where(['user_id' => Yii::$app->user->getId()])->andWhere(['status' => [Status::STATUS_UNVERIFIED, Status::STATUS_ACTIVE, Status::STATUS_DISABLE]]);
        $countOfResults = $query->count('id');

        $sortAttributes = [
            'id' => \Yii::t('app', 'id'),
            '-id' => \Yii::t('app', 'id'),
            'created_at' => \Yii::t('app', 'Created At'),
            '-created_at' => \Yii::t('app', 'Created At'),
            'title' => \Yii::t('app', 'Title'),
            '-title' => \Yii::t('app', 'Title'),
        ];
        $singleSort = new SingleSort([
            'sort' => $sort,
            'sortAttributes' => $sortAttributes,
        ]);

        $pagination = new Pagination([
            'params' => [
                'page' => $page,
                'per-page' => 12,
            ],
            'totalCount' => $countOfResults,
        ]);

        $posts = [];
        if ($countOfResults > 0) {
            $posts = $query->orderBy([$singleSort->attribute => $singleSort->order])->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        }

        return [
            'Posts' => $posts,
            'Sort' => [
                'sort' => $singleSort->sort,
                'sortAttributes' => $singleSort->sortAttributes,
            ],
            'Pagination' => [
                'page_count' => $pagination->getPageCount(),
                'page_size' => $pagination->getPageSize(),
                'page' => $pagination->getPage(),
                'total_count' => $countOfResults,
            ],
        ];
    }

    public function actionCreate()
    {
        $model = (new $this->modelClass);
        $this->handleOne($model);
        return $this->response($model);
    }

    public function actionUpdate($id)
    {
        $model = $this->findOne($id);
        $this->handleOne($model);
        return $this->response($model);
    }

    public function actionView($id)
    {
        $model = $this->findOne($id);
        return $this->response($model);
    }

    public function actionDelete($id)
    {
        $model = $this->findOne($id);
        return $this->response($model, ['status' => boolval($model->delete()), 'Post' => null, 'errors' => []]);
    }

    private function findOne($id)
    {
        $modelClass = (new $this->modelClass);
        $model = $modelClass::find()->where(['AND', ['id' => $id, 'status' => [Status::STATUS_UNVERIFIED, Status::STATUS_ACTIVE, Status::STATUS_DISABLE]], ['user_id' => Yii::$app->user->getId()]])->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        return $model;
    }

    private function handleOne($model)
    {
        $model->load(Yii::$app->request->post(), '');
        $model->image = UploadedFile::getInstanceByName('image');
        $model->save();
    }

}
