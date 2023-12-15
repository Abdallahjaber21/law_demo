<?php

use common\config\includes\P;
use common\models\Admin;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $model common\models\PlantPpmTasks */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Plant Ppm Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plant-ppm-tasks-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::C(P::PPM_PLANT_PPM_TASKS_UPDATE)) {
            ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?php if (P::C(P::PPM_PLANT_PPM_TASKS_DELETE)) { ?>
                <?php
                $panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-flat',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
            <?php } ?> <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'id',
                                'name',
                                [
                                    'attribute' => 'task_type',
                                    'value' => function ($model) {
                                        return $model->task_type_label;
                                    }
                                ],
                                'occurence_value',
                                [
                                    'attribute' => 'meter_type',
                                    'value' => function ($model) {
                                        return $model->meter_type_label;
                                    }
                                ],
                                [
                                    'attribute' => 'status',
                                    'value' => $model->status_label
                                ],
                                'created_at',
                                'updated_at',
                                [
                                    'attribute' => 'created_by',
                                    'value' => function ($model) {
                                        return @Admin::findOne($model->created_by)->name;
                                    }
                                ],
                                [
                                    'attribute' => 'updated_by',
                                    'value' => function ($model) {
                                        return @Admin::findOne($model->updated_by)->name;
                                    }
                                ],
                            ],
                        ]) ?>

            <?php PanelBox::end() ?> </div>
    </div>
</div>