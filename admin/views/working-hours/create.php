<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\WorkingHours */

$this->title = Yii::t('app', 'Create Working Hours');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Working Hours'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="working-hours-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
