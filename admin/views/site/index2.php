<?php

use common\components\extensions\ActionColumn;
use common\models\Employee;
use common\models\RepairRequest;
use common\models\Route;
use common\models\RouteAssignment;
use common\models\Truck;
use common\widgets\dashboard\PanelBox;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $from string */
/* @var $to string */

/* @var $pendingRepairRequests RepairRequest[] */
/* @var $completedRepairRequests RepairRequest[] */
/* @var $departedRepairRequests RepairRequest[] */


$this->title = 'Maintenance Dashboard';
?>
<div class="row">
    <div class="col-md-12">
        <?php
        $panel = PanelBox::begin([
            'title' => $this->title,
            'icon' => 'dashboard',
            //'body' => false,
            'color' => PanelBox::COLOR_RED
        ]);
        ?>
        <!--        --><?php //$panel->beginHeaderItem() ?>
        <!--        --><?php //Html::beginForm(['index'], 'GET') ?>
        <!--        --><?php //DateRangeFilter::widget(['auto_submit' => true]) ?>
        <!--        --><?php //Html::endForm() ?>
        <!--        --><?php //$panel->endHeaderItem() ?>


        <h3>Ongoing Maintenances</h3>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider(['allModels' => $completedRepairRequests]),
            'filterModel' => null,
            'columns' => [
                [
                    'attribute' => 'id',
                    'headerOptions' => ['style' => 'width:75px'],
                    'value'         => function ($model) {
                        if (!empty($model->related_request_id)) {
                            return Html::a("{$model->id}", ['repair-request/view', 'id' => $model->id]) . ' [Prev: ' .
                                Html::a("{$model->related_request_id}", ['repair-request/view', 'id' => $model->related_request_id])
                                . ']';
                        }
                        return Html::a("{$model->id}", ['repair-request/view', 'id' => $model->id]);
                    },
                    'format'        => 'html',
                ],
                [
                    'label' => 'customer',
                    'value' => 'equipment.location.customer.name',
                ],
                [
                    'attribute' => 'equipment_id',
                    'value' => 'equipment.name',
                ],
                [
                    'attribute' => 'technician_id',
                    'value' => 'technician.name',
                ],
                [
                    'attribute' => 'status',
                    'class' => common\components\extensions\OptionsColumn::className()
                ],
                [
                    'attribute' => 'assigned_at',
                    'format' => 'datetime',
                    'class' => common\components\extensions\DateColumn::className()
                ],
                [
                    'attribute' => 'informed_at',
                    'format' => 'datetime',
                    'class' => common\components\extensions\DateColumn::className()
                ],
                [
                    'attribute' => 'eta',
                    'format' => 'datetime',
                    'class' => common\components\extensions\DateColumn::className()
                ],
                [
                    'attribute' => 'arrived_at',
                    'format' => 'datetime',
                    'class' => common\components\extensions\DateColumn::className()
                ],

                [
                    'class' => ActionColumn::className(),
                    'template' => '{view}',
                    'headerOptions' => ['style' => 'width:33px;white-space: nowrap;'],
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t("app", 'View'),
                                'aria-label' => Yii::t("app", 'View'),
                                'data-pjax' => '0',
                                'class' => 'btn btn-xs btn-primary btn-flat'
                            ];
                            return Html::a(\Yii::t("app", 'View'), ['/repair-request/view', 'id' => $model->id], $options);
                        }
                    ]
                ],
            ],
        ]); ?>

        <h3 class="text-success">Departed Maintenances</h3>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider(['allModels' => $departedRepairRequests]),
            'filterModel' => null,
            'columns' => [
                [
                    'attribute' => 'id',
                    'headerOptions' => ['style' => 'width:75px'],
                    'value'         => function ($model) {
                        if (!empty($model->related_request_id)) {
                            return Html::a("{$model->id}", ['repair-request/view', 'id' => $model->id]) . ' [Prev: ' .
                                Html::a("{$model->related_request_id}", ['repair-request/view', 'id' => $model->related_request_id])
                                . ']';
                        }
                        return Html::a("{$model->id}", ['repair-request/view', 'id' => $model->id]);
                    },
                    'format'        => 'html',
                ],
                [
                    'label' => 'customer',
                    'value' => 'equipment.location.customer.name',
                ],
                [
                    'attribute' => 'equipment_id',
                    'value' => 'equipment.name',
                ],
//                [
//                    'attribute' => 'problem_id',
//                    'value' => function ($model) {
//                        if (empty($model->problem_id)) {
//                            if (!empty($model->problem_input)) {
//                                return "Other ({$model->problem_input})";
//                            }
//                            return null;
//                        }
//                        return $model->problem->name;
//                    }
//                ],
                [
                    'attribute' => 'technician_id',
                    'value' => 'technician.name',
                ],
                [
                    'attribute' => 'status',
                    'class' => common\components\extensions\OptionsColumn::className()
                ],
                [
                    'attribute' => 'type',
                    'class' => common\components\extensions\OptionsColumn::className()
                ],
                [
                    'attribute' => 'departed_at',
                    'format' => 'datetime',
                    'class' => common\components\extensions\DateColumn::className()
                ],

                [
                    'class' => ActionColumn::className(),
                    'template' => '{view}',
                    'headerOptions' => ['style' => 'width:33px;white-space: nowrap;'],
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t("app", 'View'),
                                'aria-label' => Yii::t("app", 'View'),
                                'data-pjax' => '0',
                                'class' => 'btn btn-xs btn-primary btn-flat'
                            ];
                            return Html::a(\Yii::t("app", 'View'), ['/repair-request/view', 'id' => $model->id], $options);
                        }
                    ]
                ],
            ],
        ]); ?>

        <hr/>

        <h3 class="text-warning">Pending Maintenances</h3>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider(['allModels' => $pendingRepairRequests]),
            'filterModel' => null,
            'columns' => [
                [
                    'attribute' => 'id',
                    'headerOptions' => ['style' => 'width:75px'],
                    'value'         => function ($model) {
                        if (!empty($model->related_request_id)) {
                            return Html::a("{$model->id}", ['repair-request/view', 'id' => $model->id]) . ' [Prev: ' .
                                Html::a("{$model->related_request_id}", ['repair-request/view', 'id' => $model->related_request_id])
                                . ']';
                        }
                        return Html::a("{$model->id}", ['repair-request/view', 'id' => $model->id]);
                    },
                    'format'        => 'html',
                ],
//                [
//                    'label' => 'Reported By (Contact)',
//                    'value' => function ($model) {
//                        if (!empty($model->user)) {
//                            return "{$model->user->name} - {$model->user->phone_number}";
//                        }
//                    }
//                ],
                [
                    'attribute' => 'equipment_id',
                    'value' => function ($model) {
                        if (!empty($model->equipment)) {
                            return "{$model->equipment->code} - {$model->equipment->name}";
                        }
                    }
                ],
                [
                    'attribute' => 'technician_id',
                    'value' => 'technician.name',
                ],
                [
                    'attribute' => 'status',
                    'class' => common\components\extensions\OptionsColumn::className()
                ],
//                [
//                    'attribute' => 'type',
//                    'class' => common\components\extensions\OptionsColumn::className()
//                ],
//                [
//                    'attribute' => 'schedule',
//                    'class' => common\components\extensions\OptionsColumn::className()
//                ],
//                [
//                    'attribute' => 'scheduled_at',
//                    'format' => 'date',
//                    'class' => common\components\extensions\DateColumn::className()
//                ],
//                'extra_cost:currency',
                [
                    'attribute' => 'created_at',
                    'format' => 'datetime',
                    'class' => common\components\extensions\DateColumn::className()
                ],
//                [
//                    'attribute' => 'assigned_at',
//                    'format' => 'datetime',
//                    'class' => common\components\extensions\DateColumn::className()
//                ],

                [
                    'class' => ActionColumn::className(),
                    'template' => '{view}',
                    'headerOptions' => ['style' => 'width:33px;white-space: nowrap;'],
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t("app", 'View'),
                                'aria-label' => Yii::t("app", 'View'),
                                'data-pjax' => '0',
                                'class' => 'btn btn-xs btn-primary btn-flat'
                            ];
                            return Html::a(\Yii::t("app", 'View'), ['/repair-request/view', 'id' => $model->id], $options);
                        }
                    ]
                ],
            ],
        ]); ?>

        <?php PanelBox::end() ?>
    </div>
</div>

<style type="text/css">
    <?php ob_start() ?>
    .content-header {
        display: none;
    }

    .content-wrapper {
        position: relative;
    }

    .content {
        padding: 0;
    }

    <?php $css = ob_get_clean() ?>
    <?php $this->registerCss($css) ?>
</style>

<script>
    <?php ob_start(); ?>
    setTimeout(function () {
        location.reload();
    }, 30000)
    <?php $js = ob_get_clean();?>
    <?php $this->registerJs($js);?>
</script>