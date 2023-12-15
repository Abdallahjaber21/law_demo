<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TechnicianLocation */

$this->title = Yii::t('app', 'Create Technician Location');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Technician Locations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="technician-location-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
