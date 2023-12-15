<?php

use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\components\extensions\Select2;
use common\config\includes\P;
use common\models\Division;
use common\models\Employee;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\RepairRequest;
use common\models\Route;
use common\models\RouteAssignment;
use common\models\search\RepairRequestSearch;
use common\models\Sector;
use common\models\Technician;
use common\models\Truck;
use common\models\users\Admin;
use common\widgets\dashboard\DashboardDateRangeFilter;
use common\widgets\dashboard\PanelBox;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use kartik\mpdf\Pdf;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$todaysServices = [];
$upcomingDays = [];

$this->title = 'Labor Charge';
?>
<div class="monthly-dashboard-index" style="padding-top: 2rem;">
    <div class="row" style="margin: 0 !important;">
        <div class="col-md-12">
            <?php
            $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'dashboard',
                'color' => PanelBox::COLOR_GREEN,
            ]);
            ?>
            <?php if (P::c(P::REPORT_LABOR_CHARGE_PAGE_EXPORT)) {
            ?>
                <div class="form-group">
                    <?php $panel->addButton(
                        Yii::t('app', 'Export'),
                        array_merge(['export/repair-requests'], Yii::$app->request->get()),
                        [
                            'class' => 'btn btn-success btn-flat',
                            'data-method' => 'POST'
                        ]
                    );
                    ?>
                </div>
            <?php } ?>

            <?= Html::endForm() ?>
            <?php PanelBox::end() ?>
            <?php $panel = PanelBox::begin([
                'title' => 'Work Orders',
                'icon' => 'calendar',
                'color' => PanelBox::COLOR_BLUE,
                'canMinimize' => true,
                'panelClass' => 'Collapsible_flex_panel'
            ]);
            ?>

            <?= GridView::widget([
                // 'dataProvider' => new ArrayDataProvider(['allModels' => $dataProvider]),
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'rowOptions' => function (RepairRequest $model, $key, $index, $column) {
                    if (!empty($model->pending_equipment_id)) {
                        return ['class' => 'warning'];
                    }
                },
                'tableOptions' => ['class' => 'table table-bordered'],
                'rowOptions' => function ($model, $key, $index, $column) {
                    if ($model->urgent_status == true) {
                        return ['class' => 'danger'];
                    }
                },
                'columns' => [
                    [
                        'attribute' => 'id',
                        'value' => function ($model) {
                            return Html::a($model->id, Url::to(['repair-request/view', 'id' => $model->id]));
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'service_type',
                        'label' => 'Type',
                        'value' => function ($model) {
                            return $model->service_type_label;
                        },
                        'class' => common\components\extensions\OptionsColumn::class,

                    ],
                    [
                        'attribute' => 'labor_charge',
                        'value' => function ($model) {
                            return $model->labor_charge;
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class,

                        'value' => function ($model) {
                            return $model->status_label;
                        }
                    ],
                    [
                        'attribute' => 'equipment_id',
                        'value' => function ($model) {
                            if (!empty($model->equipment_id)) {
                                $equipment = $model->equipment;
                                return $equipment->code . ' | ' . $equipment->equipment->name . ' | ' . $equipment->equipment->category->name;
                            }
                        },
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'division_id',
                        'value' => function ($model) {

                            if (!empty($model->division_id)) {
                                return $model->division->name;
                            }
                        },
                        'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),

                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'location_id',
                        'value' => function ($model) {

                            if (!empty($model->location_id))
                                return $model->location->name;
                        },
                        'data' => ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),

                    ],

                    [
                        'attribute' => 'repair_request_path',
                        'value' => function ($model) {
                            return $model->repair_request_path;
                        },
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'owner_id',
                        'value' => function ($model) {

                            if (!empty($model->owner_id))
                                return $model->owner->name;
                        },
                        'data' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')


                    ],
                    [
                        'attribute' => 'assignees',
                        'value' => function ($model) {
                            return $model->getAssigneesDetails();
                        },
                        'format' => 'raw',
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'team_leader_id',
                        'value' => function ($model) {

                            if (!empty($model->team_leader_id))
                                return $model->teamLeader->name;
                        },
                        'data' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')

                    ],
                    [
                        'attribute' =>  'created_at',
                        'class' => common\components\extensions\DateColumn::class,
                    ],
                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'created_by',
                        'label'     => 'Created By',
                        'value'     => function ($model) {
                            return $model->getBlamable($model->created_by);
                        },
                        'data' => array_merge(
                            ['Admins' =>  ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                            ['Tehnicians' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                        ),

                    ],
                    [
                        'attribute' =>  'completed_at',
                        'class' => common\components\extensions\DateColumn::class,
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
                                    'class' => 'btn btn-xs btn-info btn-flat'
                                ];
                                return Html::a(Yii::t("app", 'View'), ['/repair-request/view', 'id' => $model->id], $options);
                            }
                        ],
                        'permissions' => [
                            'view' => P::REPAIR_REPAIR_DASHBOARD_PAGE_VIEW
                        ]
                    ],
                ],
            ]); ?>


            <?php PanelBox::end() ?>


        </div>
    </div>

</div>

<style type="text/css">
    <?php ob_start() ?>.content-header {
        display: none;
    }

    .box {

        box-shadow: inset 0px 0px 5px #a9a9a9fa;
    }

    .form-inline .form-group {
        display: flex
    }

    .content-wrapper {
        position: relative;
    }

    .content {
        padding: 0;
    }

    .grid-view tr td:last-child {
        text-align: left;
    }


    <?php $css = ob_get_clean() ?><?php $this->registerCss($css) ?>
</style>