<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EquipmentCaValue */

$this->title = 'Create Equipment Ca Value';
$this->params['breadcrumbs'][] = ['label' => 'Equipment Ca Values', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-ca-value-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
