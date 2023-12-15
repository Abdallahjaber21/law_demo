<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RemovalRequest */

$this->title = 'Update Removal Request: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Removal Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="removal-request-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
