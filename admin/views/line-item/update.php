<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LineItem */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Line Item',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Repair Requests'), 'url' => ['repair-request/index']];
$this->params['breadcrumbs'][] = ['label' => "#".$model->repair_request_id, 'url' => ['repair-request/view', 'id' => $model->repair_request_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update Line item');
?>
<div class="line-item-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
