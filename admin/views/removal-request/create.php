<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RemovalRequest */

$this->title = 'Create Removal Request';
$this->params['breadcrumbs'][] = ['label' => 'Removal Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="removal-request-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
