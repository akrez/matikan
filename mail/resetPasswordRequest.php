<?php

use yii\helpers\Html;

?>

<div class="user">
    <p>
        <?= Html::encode($name) ?>
    </p>
    <p>
        <?= $label ?> <br> <?= $reset_token ?>
    </p>
</div>