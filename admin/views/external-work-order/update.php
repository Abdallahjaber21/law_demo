<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RepairRequest */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Work Orders',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'External Work Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="repair-request-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>