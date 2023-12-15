<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LoginAudit */

$this->title = 'Create Login Audit';
$this->params['breadcrumbs'][] = ['label' => 'Login Audits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-audit-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
