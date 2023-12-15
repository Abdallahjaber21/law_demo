<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RepairRequestChats */

$this->title = 'Create Repair Request Chats';
$this->params['breadcrumbs'][] = ['label' => 'Repair Request Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="repair-request-chats-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
