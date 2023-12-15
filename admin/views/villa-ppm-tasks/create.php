<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VillaPpmTasks */

$this->title = 'Create Villa Ppm Tasks';
$this->params['breadcrumbs'][] = ['label' => 'Villa Ppm Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="villa-ppm-tasks-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
