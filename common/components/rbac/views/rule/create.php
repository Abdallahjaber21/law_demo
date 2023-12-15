<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\components\rbac\models\AuthItem */

?>
<div class="auth-item-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
