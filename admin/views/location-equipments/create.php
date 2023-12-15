<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LocationEquipments */

$this->title = 'Create Location Equipments';
$this->params['breadcrumbs'][] = ['label' => 'Location Equipments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-equipments-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>