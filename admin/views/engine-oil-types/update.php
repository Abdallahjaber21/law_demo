<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EngineOilTypes */

$this->title = 'Update Engine Oil Types: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Engine Oil Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="engine-oil-types-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
