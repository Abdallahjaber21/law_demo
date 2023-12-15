<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TechnicianShift */

$this->title = 'Update Technician Shift: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Technician Shifts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="technician-shift-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
