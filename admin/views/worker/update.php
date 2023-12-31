<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Worker */

$this->title = 'Update Worker: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Workers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="worker-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
