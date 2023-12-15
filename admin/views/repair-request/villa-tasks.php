<?php

use common\models\VillaPpmTasksHistory;
use common\widgets\dashboard\PanelBox;
use yii\helpers\Html;

$this->title = "Repair Order: " . $model->id . " tasks";
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$service_tasks = $model->getVillaPpmTasks();
?>


<div class="row">
    <?php if (count($service_tasks['villa_tasks']) > 0): ?>
        <div class="col-sm-12">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode("Tasks"),
                'icon' => 'file-code-o',
                'color' => PanelBox::COLOR_GREEN,
            ]);
            ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="column-action">Action</th>
                        <th class="column-task">Task</th>
                        <th class="column-remark">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($service_tasks['villa_tasks'] as $task): ?>
                        <tr>
                            <td class="column-action">
                                <?= (new VillaPpmTasksHistory())->status_list[$task['current_status']] ?>
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
</div>