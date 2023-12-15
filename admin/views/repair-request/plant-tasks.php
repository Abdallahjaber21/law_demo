<?php

use common\models\Equipment;
use common\models\EquipmentType;
use common\models\MallPpmTasksHistory;
use common\models\RepairRequest;
use common\models\PlantPpmTasks;
use common\models\PlantPpmTasksHistory;
use common\models\VehicleOilChangeHistory;
use common\widgets\dashboard\PanelBox;
use common\widgets\inputs\assets\ICheckAsset;
use rmrevin\yii\fontawesome\FA;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = "Repair Order: " . $model->id . " tasks";
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$service_tasks = $model->getPlantPpmTasks();

?>


<div class="row plan_ppm_tasks_section">

    <?php if (count($service_tasks['plant_tasks']) > 0): ?>
        <div class="col-sm-6">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode("Tasks"),
                'icon' => 'file-code-o',
                'color' => PanelBox::COLOR_BLUE,
            ]);
            ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="column-action">Status</th>
                        <th class="column-task">Task</th>
                        <th class="column-remark">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($service_tasks['plant_tasks'] as $task): ?>
                        <?php
                        $oil = null;

                        if ($task['task_id'] == 1 && $task['current_status'] == PlantPpmTasksHistory::TASK_STATUS_REPLACE) {
                            $oil_model = VehicleOilChangeHistory::find()->where(['repair_request_id' => $model->id])->one();

                            if (!empty($oil_model)) {
                                $oil .= " Oil Type:" . @$oil_model->oil->oil_viscosity;
                            }

                            $oil = !empty($oil) ? $oil : null;
                        }
                        ?>
                        <tr>
                            <td class="column-action">
                                <?= (new PlantPpmTasksHistory())->task_status_list[$task['current_status']] ?>
                            </td>
                            <td class="column-task">
                                <?= $task['name'] ?>
                            </td>
                            <td class="column-remark">
                                <div class="item-inner no-before no-after" style="padding-top: 0 !important;">
                                    <div class="item-input-wrap">
                                        <textarea placeholder="Remarks" class="form-control"
                                            readonly><?= $task['remark'] . $oil ?></textarea>
                                        <span class="input-clear-button"></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php $panel->end(); ?>
        </div>
    <?php endif; ?>
    <?php if (count($service_tasks['checklist_tasks']) > 0): ?>
        <div class="col-sm-6">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode("Checklist"),
                'icon' => 'file-code-o',
                'color' => PanelBox::COLOR_GREEN,
            ]);
            ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="column-action">Status</th>
                        <th class="column-task">Task</th>
                        <th class="column-remark">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($service_tasks['checklist_tasks'] as $task): ?>
                        <tr>
                            <td class="column-action">
                                <?= (new PlantPpmTasksHistory())->task_status_list[$task['current_status']] ?>
                            </td>
                            <td class="column-task">
                                <?= $task['name'] ?>
                            </td>
                            <td class="column-remark">
                                <div class="item-inner no-before no-after" style="padding-top: 0 !important;">
                                    <div class="item-input-wrap">
                                        <textarea placeholder="Remarks" class="form-control"
                                            readonly><?= $task['remark'] ?></textarea>
                                        <span class="input-clear-button"></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php $panel->end(); ?>
        </div>
    <?php endif; ?>


    <?php if (@$model->equipment->equipment->equipmentType->meter_type == EquipmentType::METER_TYPE_KM || $choose_tasks): ?>
        <?php
        $current_ids = ArrayHelper::merge(ArrayHelper::getColumn($service_tasks['plant_tasks'], 'task_id'), ArrayHelper::getColumn($service_tasks['checklist_tasks'], 'task_id'));

        $other_tasks = PlantPpmTasks::find()->where(['NOT IN', 'id', $current_ids])->asArray()->all();

        // print_r($other_tasks);
    
        // [
        //     'class' => 'yii\grid\CheckboxColumn',
        //     'headerOptions' => ['class' => 'checkbox-header'],
        //     'checkboxOptions' => function ($technician_model, $key, $index, $column) use ($model) {
        //         $assignee = @Assignee::find()->where(['repair_request_id' => $model->id, 'user_id' => $technician_model->id])->one();
        //         return ['id' => 'check-' . $technician_model->id, 'name' => '[' . $technician_model->id . ']', 'class' => 'checkbox-header', 'value' => $technician_model->id, 'checked' => !empty($assignee) ? true : false];
        //     },
        //     'visible' => $model->status != RepairRequest::STATUS_COMPLETED
        // ],
    
        $new_data_provider_add_tasks = new ArrayDataProvider([
            'allModels' => $other_tasks,
        ]);
        ?>
        <div class="col-sm-<?= $choose_tasks ? "12" : "6" ?>">
            <?php

            echo Html::beginForm(
                yii\helpers\Url::to(['repair-request/add-plant-ppm-tasks', 'id' => $model->id]),
                'post'
            );

            $panel = PanelBox::begin([
                'title' => Html::encode("Add Tasks"),
                'icon' => 'plus',
                'color' => PanelBox::COLOR_ORANGE,
            ]);

            $panel->beginHeaderItem();

            if (count($other_tasks) > 0 && ($status != RepairRequest::STATUS_COMPLETED && $status != RepairRequest::STATUS_REQUEST_COMPLETION))
                echo Html::submitButton('Save', ['class' => 'btn btn-sm btn-success', 'data-method' => 'post']);

            $panel->endHeaderItem();
            ?>
            <?php

            $isReadOnly = "true";
            if ($status == RepairRequest::STATUS_COMPLETED || $status == RepairRequest::STATUS_REQUEST_COMPLETION) {
                $isReadOnly = "false";
            }
            ?>
            <?= GridView::widget([
                'dataProvider' => $new_data_provider_add_tasks,
                'filterModel' => null,
                'summary' => false,
                //$new_search_model,
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'headerOptions' => ['class' => 'checkbox-header'],
                        'checkboxOptions' => function ($model, $key, $index, $column) use ($isReadOnly) {
                            $checkboxOptions = ['id' => 'check-' . $model['id'], 'name' => '[' . $model['id'] . ']', 'class' => 'checkbox-header', 'value' => json_encode($model)];
                            if ($isReadOnly == "false") {
                                $checkboxOptions['disabled'] = 'disabled';
                            }
                            return $checkboxOptions;
                        },
                        'visible' => count($other_tasks) > 0,
                    ],
                    'name',
                    'occurence_value',
                    [
                        'attribute' => 'task_type',
                        'value' => function ($model) {
                            return (new PlantPpmTasks())->task_type_list[$model['task_type']];
                        }
                    ],
                ],
            ]); ?>

            <?php $panel->end(); ?>

            <?php Html::endForm(); ?>
        </div>
    <?php endif; ?>
</div>