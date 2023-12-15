<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentCaValue */

$this->title = 'Update Equipment Ca Value: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Equipment Ca Values', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="equipment-ca-value-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
