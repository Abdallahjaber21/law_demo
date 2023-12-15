<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TechnicianLocation */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Technician Location',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Technician Locations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="technician-location-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
