<?php


/* @var $this View */
/* @var $model RemovalRequest */

use common\models\RemovalRequest;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

\common\widgets\dashboard\PanelBox::begin([
    'title' => "Provide a rejection reason",
    'color' => \common\widgets\dashboard\PanelBox::COLOR_RED,
]);
$form = ActiveForm::begin([
    'action' => ['reject-form', 'id' => $model->id]
]);
echo Html::tag("label", "Rejection Reaons", ['class'=>'control-label']);
echo Html::textarea('reject-reason',null,['class'=>'form-control']);
echo "<br/>";
echo Html::submitButton("Reject", ['class' => 'btn btn-danger pull-right']);
ActiveForm::end();
\common\widgets\dashboard\PanelBox::end();
?>

