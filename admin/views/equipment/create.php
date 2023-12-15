<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Equipment */

$this->title = Yii::t('app', 'Create Equipment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Equipments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
