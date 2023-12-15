<?php

use common\models\MallPpmTasksHistory;
use common\widgets\dashboard\PanelBox;
use yii\helpers\Html;

$this->title = "Repair Order: " . $model->id . " tasks";
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$service_tasks = $model->getMallPpmTasks();
?>


<div class="row">
    <div class="col-sm-5">
        <?php
        $panel = PanelBox::begin([
            'title' => Html::encode("Tasks"),
            'icon' => 'file-code-o',
            'color' => PanelBox::COLOR_ORANGE,
        ]);
        ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="label-cell text-15">Task</th>
                    <th class="label-cell text-15">Completed</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($service_tasks['tasks'] as $task): ?>
                    <tr>
                        <td class="label-cell label_task_name">
                            <?= $task['name'] ?>
                        </td>
                        <td class="label-cell actions_cell">
                            <?= (new MallPpmTasksHistory())->task_status_list[$task['current_status']]; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
        <?php $panel->end(); ?>
    </div>
    <div class="col-sm-4">
        <?php
        $panel = PanelBox::begin([
            'title' => Html::encode("Additional Tasks"),
            'icon' => 'plus',
            'color' => PanelBox::COLOR_BLUE,
        ]);
        ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="label-cell text-15">Task</th>
                    <th class="label-cell text-15" style="border-bottom: 1px solid whitesmoke;">Answer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($service_tasks['additional_tasks'] as $key => $task): ?>
                    <tr>
                        <td class="label-cell label_task_name text-bold text-15" style="white-space: nowrap;">
                            <?= $key ?>
                        </td>
                    </tr>
                    <tr>
                        <?php foreach ($task as $taskim): ?>
                            <td>
                                <?= $taskim['service'] ?>
                            </td>
                            <td>
                                <div class="item-inner">
                                    <div class="item-input-wrap">
                                        <input type="text" class="form-control" readonly placeholder="Answer"
                                            value="<?= $taskim['value'] ?>">
                                        <span class="input-clear-button"></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php $panel->end(); ?>
    </div>
    <div class="col-sm-3">
        <?php
        $panel = PanelBox::begin([
            'title' => Html::encode($service_tasks['additional_tasks_remark']['name']),
            'icon' => 'plus',
            'color' => PanelBox::COLOR_RED,
        ]);
        ?>
        <table class="table table-striped table-bordered">
            <tr>
                <td>
                    <div class="item-inner">
                        <div class="item-input-wrap">
                            <textarea class="form-control" readonly placeholder="Answer" rows="9"
                                style="height: fit-content;"><?= $service_tasks['additional_tasks_remark']['value'] ?></textarea>
                            <span class="input-clear-button"></span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <?php $panel->end(); ?>
    </div>
</div>