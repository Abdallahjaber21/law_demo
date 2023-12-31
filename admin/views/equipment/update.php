<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Equipment */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Equipment',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Equipments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="equipment-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
