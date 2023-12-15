<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Manufacturer */

$this->title = Yii::t('app', 'Create Manufacturer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manufacturers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manufacturer-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
