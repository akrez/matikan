<?php

use yii\helpers\Html;

?>

<div class="user">
    <p>
        <?= Html::encode($name) ?>
    </p>
    <p>
        <?= $label ?> <br> <?= $resetToken ?>
    </p>
</div>