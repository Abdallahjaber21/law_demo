<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProfessionCategory */

$this->title = 'Update Profession Category: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Profession Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="profession-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
