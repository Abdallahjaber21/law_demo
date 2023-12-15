<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\State */

$this->title = 'Create State';
$this->params['breadcrumbs'][] = ['label' => 'States', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="state-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
