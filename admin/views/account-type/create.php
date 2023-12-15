<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AccountType */

$this->title = 'Create Account Type';
$this->params['breadcrumbs'][] = ['label' => 'Account Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-type-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
