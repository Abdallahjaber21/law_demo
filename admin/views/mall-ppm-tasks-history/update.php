<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MallPpmTasksHistory */

$this->title = 'Update Mall Ppm Tasks History: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mall Ppm Tasks Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mall-ppm-tasks-history-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
