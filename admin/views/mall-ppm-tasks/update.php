<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MallPpmTasks */

$this->title = 'Update Mall Ppm Tasks';
$this->params['breadcrumbs'][] = ['label' => 'Mall Ppm Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mall-ppm-tasks-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>