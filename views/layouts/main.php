<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\SiteAsset;
use app\widgets\Alert;

$this->title = ($this->title ? $this->title : Yii::$app->name);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Yii::$app->view->registerLinkTag(['rel' => 'icon', 'href' => Yii::getAlias('@web/resources/favicon.png')]) ?>
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>

    <body>
        <?php $this->beginBody() ?>
        <div class="container">

            <div class="row">
                <div class="col-sm-12">
                    <?= $content ?>
                </div>
            </div>

        </div>
        <?php $this->endBody() ?>
    </body>

</html>
<?php $this->endPage() ?>
