<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CoordinatesIssue */

$this->title = 'Update Coordinates Issue: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Coordinates Issues', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="coordinates-issue-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
