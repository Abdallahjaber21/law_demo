<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ObjectCategory */

$this->title = Yii::t('app', 'Create Object Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Object Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
