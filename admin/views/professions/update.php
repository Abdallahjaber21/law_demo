<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Professions */

$this->title = 'Update Professions: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Professions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="professions-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
