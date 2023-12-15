<?php


/* @var $this View */

use common\models\UserLocation;
use common\widgets\dashboard\PanelBox;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\View;

/* @var $model array|UserLocation|null|ActiveRecord */

$location = $model->location;
$removedUnits = explode(",", $model->removed_units);
?>
<?php
$panel = PanelBox::begin([
    'title' => $model->user->name,
    'icon'  => 'eye',
    'color' => PanelBox::COLOR_RED
]);
?>
<?php
//TODO ADD FORM AND SUBMIT TO SAVE UNWANTED UNITS
?>
<?= Html::beginForm(['location/update-user-locations', 'id'=> $model->id]) ?>
<div>
    <?= \common\widgets\inputs\ICheck::widget([
        'name'  => "is_locked",
        'value' => $model->is_locked,
        'options' => [
            'id' => "is_locked_check",
            'class'=>'form-control'
        ]
    ]) ?>
    <label for="is_locked_check">Lock user from activating/deactivating units?</label>
</div>
<hr/>
<?php foreach ($location->equipments as $index => $equipment) { ?>
    <div class="form-group">
        <?= \common\widgets\inputs\ICheck::widget([
            'name'  => "unit[$equipment->id]",
            'value' => !in_array($equipment->id, $removedUnits),
            'options' => [
                'id' => "check-{$equipment->id}",
                'class'=>'form-control'
            ]
        ]) ?>
        <label for="<?= "check-{$equipment->id}" ?>"><?= $equipment->name ?></label>
    </div>
<?php } ?>
<div>
    <?= Html::submitButton('Save',['class'=>'btn btn-lg btn-primary btn-block']) ?>
</div>
<?= Html::endForm() ?>
<?php PanelBox::end() ?>
