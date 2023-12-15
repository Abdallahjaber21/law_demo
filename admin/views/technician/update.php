<?php

use yii\helpers\Html;
use common\models\Account;

/* @var $this yii\web\View */
/* @var $model common\models\Technician */

$this->title = 'Update ' . Account::getTechnicianTypeLabel($model->id) . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Technicians', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="technician-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>