<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SegmentPath */

$this->title = 'Create Segment Path';
$this->params['breadcrumbs'][] = ['label' => 'Segment Paths', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="segment-path-create">

    <?= $this->render('_form', [
        'model' => $model,
        'segment_pathes_model' => $segment_pathes_model
    ]) ?>

</div>