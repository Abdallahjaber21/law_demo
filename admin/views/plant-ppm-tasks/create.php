<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PlantPpmTasks */

$this->title = 'Create Plant Ppm Tasks';
$this->params['breadcrumbs'][] = ['label' => 'Plant Ppm Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plant-ppm-tasks-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
