<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RepairRequestChats */

$this->title = 'Update Repair Request Chats: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Repair Request Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="repair-request-chats-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
