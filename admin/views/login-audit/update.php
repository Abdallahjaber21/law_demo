<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LoginAudit */

$this->title = 'Update Login Audit: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Login Audits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="login-audit-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
