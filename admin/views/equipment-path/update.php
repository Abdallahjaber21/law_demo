<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentPath */

$this->title = 'Update Equipment Path: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Equipment Paths', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="equipment-path-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
