<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CauseCode */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Cause Code',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cause Codes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="cause-code-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
