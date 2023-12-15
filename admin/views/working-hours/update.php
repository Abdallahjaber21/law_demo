<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WorkingHours */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Working Hours',
]) . $model->year_month;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Working Hours'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->year_month];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="working-hours-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
