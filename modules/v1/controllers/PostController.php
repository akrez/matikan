<?php

namespace app\modules\v1\controllers;

use app\components\SingleSort;
use app\controllers\Controller;
use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use app\modules\v1\models\Post;

class PostController extends Controller
{

    public function behaviors()
    {
        return self::defaultBehaviors([
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
        ]);
    }

    public function actionIndex()
    {
        $userId = Yii::$app->user->getId();

        $page = Yii::$app->request->get('page');
        $sort = Yii::$app->request->get('sort');

        $query = Post::findOwn($userId);
        $countOfResults = $query->count('id');

        $singleSort = new SingleSort([
            'sort' => $sort,
            'sortAttributes' => [
                '-id' => 'شناسه (نزولی)',
                'id' => 'شناسه (صعودی)',
                '-created_at' => 'جدید‌ترین',
                'created_at' => 'قدیمی‌ترین',
                '-title' => 'عنوان (نزولی)',
                'title' => 'عنوان (صعودی)',
            ],
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
            $posts = $query
                    ->orderBy([$singleSort->attribute => $singleSort->order])
                    ->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->asArray()
                    ->all();
        }

        return [
            'posts' => $posts,
            'sort' => [
                'sort' => $singleSort->sort,
                'sort_attributes' => $singleSort->sortAttributes,
            ],
            'pagination' => [
                'page_count' => $pagination->getPageCount(),
                'page_size' => $pagination->getPageSize(),
                'page' => $pagination->getPage(),
                'total_count' => $countOfResults,
            ],
            'status' => boolval($countOfResults > 0),
        ];
    }

    public function actionCreate()
    {
        $model = new Post();
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
        return ['status' => boolval($model->delete())];
    }

    private function findOne($id)
    {
        $userId = Yii::$app->user->getId();
        $model = Post::findOwn($userId)->andWhere(['id' => $id])->one();
        if ($model) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    private function handleOne($model)
    {
        $model->load(Yii::$app->request->post(), '');
        $model->image = UploadedFile::getInstanceByName('image');
        $model->save();
    }

    public function response($model)
    {
        return [
            'status' => boolval(!$model->hasErrors()),
            'post' => $model->toArray(),
            'errors' => $model->getErrors(),
        ];
    }

}
