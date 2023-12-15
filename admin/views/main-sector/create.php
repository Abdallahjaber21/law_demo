<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MainSector */

$this->title = 'Create Main Sector';
$this->params['breadcrumbs'][] = ['label' => 'Main Sectors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="main-sector-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
