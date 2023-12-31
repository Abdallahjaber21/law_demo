<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Division */

$this->title = 'Update Division: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Divisions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="division-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
