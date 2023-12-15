<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentMaintenanceBarcode */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Equipment Maintenance Barcode',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Equipment Maintenance Barcodes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="equipment-maintenance-barcode-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
