<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ObjectCode */

$this->title = Yii::t('app', 'Create Object Code');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Object Codes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-code-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
