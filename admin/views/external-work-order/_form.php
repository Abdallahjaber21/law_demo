<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yeesoft\multilingual\widgets\ActiveForm;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\models\Account;
use common\models\AccountType;
use common\models\Category;
use common\models\Division;
use common\models\Assignee;
use common\models\Profession;
use common\models\Equipment;
use common\models\Location;
use common\models\Technician;
use common\widgets\inputs\assets\ICheckAsset;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\RepairRequest */
/* @var $form ActiveForm */

$existingAssignees = $model->assignees;


$out = [];

if (!empty($existingAssignees)) {
    foreach ($existingAssignees as $assignee) {
        if ($assignee->user->division_id == Division::DIVISION_VILLA && $assignee->user->account->type0->name != 'supervisor') {
            $out[] = $assignee;
        }
    }
}
?>
<div class="repair-request-form">
    <div class="row">
        <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off']]); ?>
        <?php print_r($form->errorSummary($model)); ?>
        <div class="col-md-6 col-md-offset-3">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php $panel->beginHeaderItem() ?>
            <h5 id="selected_location_division_sector">
                <?php if (!empty($model->division_id)) { ?>
                <strong><?= $model->division->name ?></strong>
                <?php } ?>

            </h5>
            <?php $panel->endHeaderItem() ?>
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    $panel = PanelBox::begin([
                        'title' => Html::encode('Repair Request Details'),
                        'icon' => 'wrench',
                        'color' => PanelBox::COLOR_BLUE
                    ]);
                    ?>
                    <div class="col-sm-12">
                        <label><strong>Status: </strong><?= $model->status_label ?></label>
                    </div>
                    <div class="col-sm-12">
                        <label><strong>Repair Request Path: </strong><?= $model->repair_request_path ?></label>

                    </div>

                    <div class="col-sm-12">
                        <label><strong>Supervisor: </strong><?= $model->owner->name ?></label>
                    </div>
                    <div class="col-sm-12">
                        <label><strong>Service Type: </strong><?= $model->service_type_label ?></label>
                    </div>
                    <div class="col-sm-12">
                        <label><strong>Scheduled For: </strong><?= $model->scheduled_at ?></label>
                    </div>
                    <div class="col-sm-12">
                        <label><strong>Problem: </strong><?= $model->problem ?></label>
                    </div>
                    <?php $panel->end(); ?>

                </div>
                <div class="col-md-12">
                    <?php
                    $panel = PanelBox::begin([
                        'title' => Html::encode('Location-Equipment'),
                        'icon' => 'map',
                        'color' => PanelBox::COLOR_ORANGE
                    ]);
                    ?>
                    <?php if (!empty($model->location->id)) { ?>
                    <div class="col-sm-12">
                        <label><strong>Location: </strong><?= $model->location->name ?></label>
                        <?php } ?>
                    </div>
                    <div class="col-sm-12">
                        <label><strong>Category: </strong><?= @$model->category->name ?></label>

                    </div>
                    <div class="col-sm-12">
                        <label><strong>Equipment: </strong><?php
                                                                if (!empty($model->equipment_id)) {
                                                                    $equipment = $model->equipment;
                                                                    echo $equipment->code . ' | ' . $equipment->equipment->name . ' | ' . $equipment->equipment->category->name;
                                                                } ?></label>

                    </div>
                    <?php $panel->end(); ?>
                </div>

                <div class="col-sm-12">
                    <?php
                    $panel = PanelBox::begin([
                        'title' => Html::encode('Assignees'),
                        'icon' => 'users',
                        'color' => PanelBox::COLOR_BLUE
                    ]);
                    ?>
                    <label class="control-label"><strong>Assignees</strong></label>
                    <?php

                    $data = Technician::find()->joinWith(["account", "account.type0"])
                        ->leftJoin(Assignee::tableName(), 'assignee.user_id = technician.id AND assignee.status !=' . Assignee::STATUS_FREE)
                        ->select([
                            "count(assignee.id) as count",
                            Technician::tableName() . ".id",
                        ])
                        ->where([
                            Technician::tableName() . '.division_id' => Division::DIVISION_VILLA,
                            AccountType::tableName() . '.for_backend' => false,

                        ])
                        ->andWhere([
                            'AND',
                            [
                                '!=', AccountType::tableName() . '.name', 'supervisor'
                            ],
                        ])
                        ->groupBy(['technician.id'])
                        ->asArray()
                        ->all();

                    ?>
                    <?= Select2::widget([
                        'name' => 'technician_id',
                        'options' => ['multiple' => true, 'placeholder' => 'Select Assignees'],
                        'data' => ArrayHelper::map($data, 'id', function ($model) {
                            $technician = Technician::findOne($model['id']);
                            return "{$technician->name} | {$technician->profession->name} | {$technician->account->type0->label} ({$model['count']})";
                        }),
                        'value' => ArrayHelper::getColumn($out, 'user_id')
                    ]) ?>



                    <p class="help-block help-block-error"></p>
                    <?php $panel->end(); ?>

                </div>

                <?php $panel->end(); ?>


                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' =>  'btn btn-primary btn-flat', 'style' => 'margin:10px']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <STYLE>
    label {
        font-weight: 400;
        font-size: 16px;
    }
    </STYLE>