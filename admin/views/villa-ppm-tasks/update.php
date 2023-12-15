<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VillaPpmTasks */

$this->title = 'Update Villa Ppm Tasks: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Villa Ppm Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="villa-ppm-tasks-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
