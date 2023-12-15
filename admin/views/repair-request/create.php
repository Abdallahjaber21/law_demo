<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RepairRequest */

$type = Yii::$app->request->get('type');
$service_type_label = 'work';
// if($type == \common\models\RepairRequest::TYPE_SCHEDULED){
//     $type_label = 'work';
// }
$this->title = "Create work order";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Work Orders'), 'url' => ['site/works-dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="repair-request-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>