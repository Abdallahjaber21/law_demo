<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Technician */

$this->title = 'Create Technician';
$this->params['breadcrumbs'][] = ['label' => 'Technicians', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="technician-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
