<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LocationEquipments */

$this->title = 'Update Location Equipments: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Location Equipments', 'url' => ['index', 'location_id' => $model->location_id]];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="location-equipments-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>