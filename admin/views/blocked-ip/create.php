<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\BlockedIp */

$this->title = 'Create Blocked Ip';
$this->params['breadcrumbs'][] = ['label' => 'Blocked Ips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blocked-ip-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
