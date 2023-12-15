<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Problem */

$this->title = Yii::t('app', 'Create Problem');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="problem-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
