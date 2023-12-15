<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SegmentPath */

$this->title = 'Update Segment Path: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Segment Paths', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="segment-path-update">

    <?= $this->render('_form', [
        'model' => $model,
        'segment_pathes_model' => $segment_pathes_model
    ]) ?>

</div>