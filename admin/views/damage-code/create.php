<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DamageCode */

$this->title = Yii::t('app', 'Create Damage Code');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Damage Codes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="damage-code-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
