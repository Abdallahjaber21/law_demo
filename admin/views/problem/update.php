<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Problem */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Problem',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="problem-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
