<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LineItem */

$this->title = Yii::t('app', 'Create Line Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Line Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-item-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
