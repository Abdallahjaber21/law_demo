<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MallPpmTasksHistory */

$this->title = 'Create Mall Ppm Tasks History';
$this->params['breadcrumbs'][] = ['label' => 'Mall Ppm Tasks Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-ppm-tasks-history-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
