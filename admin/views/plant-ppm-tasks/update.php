<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PlantPpmTasks */

$this->title = 'Update Plant Ppm Tasks: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Plant Ppm Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="plant-ppm-tasks-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
