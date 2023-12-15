<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MainSector */

$this->title = 'Update Main Sector: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Main Sectors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="main-sector-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>