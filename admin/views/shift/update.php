<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Shift */

$this->title = 'Update Shift: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Shifts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shift-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
