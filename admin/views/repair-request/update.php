<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RepairRequest */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Work Orders',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Work Orders'), 'url' => ['site/works-dashboard']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="repair-request-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>