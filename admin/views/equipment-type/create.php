<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EquipmentType */

$this->title = 'Create Equipment Type';
$this->params['breadcrumbs'][] = ['label' => 'Equipment Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-type-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
