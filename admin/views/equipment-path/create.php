<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EquipmentPath */

$this->title = 'Create Equipment Path';
$this->params['breadcrumbs'][] = ['label' => 'Equipment Paths', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-path-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
