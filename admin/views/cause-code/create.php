<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CauseCode */

$this->title = Yii::t('app', 'Create Cause Code');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cause Codes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cause-code-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
