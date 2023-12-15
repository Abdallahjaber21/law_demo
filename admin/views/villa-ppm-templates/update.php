<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VillaPpmTemplates */

$this->title = 'Update Villa Ppm Templates: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Villa Ppm Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="villa-ppm-templates-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
