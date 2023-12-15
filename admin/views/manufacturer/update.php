<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Manufacturer */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Manufacturer',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manufacturers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="manufacturer-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
