<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ObjectCode */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Object Code',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Object Codes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="object-code-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
