<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BlockedIp */

$this->title = 'Update Blocked Ip: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Blocked Ips', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="blocked-ip-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
