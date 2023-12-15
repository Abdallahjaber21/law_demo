<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EquipmentMaintenanceBarcode */

$this->title = Yii::t('app', 'Create Equipment Maintenance Barcode');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Equipment Maintenance Barcodes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-maintenance-barcode-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
