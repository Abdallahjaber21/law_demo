<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MallPpmTasks */

$this->title = 'Create Mall Ppm Tasks';
$this->params['breadcrumbs'][] = ['label' => 'Mall Ppm Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-ppm-tasks-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
