<?php

use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\components\extensions\Select2;
use common\config\includes\P;
use common\models\Account;
use common\models\Division;
use common\models\Employee;
use common\models\EngineOilTypes;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\RepairRequest;
use common\models\Route;
use common\models\RouteAssignment;
use common\models\search\RepairRequestSearch;
use common\models\Sector;
use common\models\Technician;
use common\models\Truck;
use common\models\Admin;
use common\widgets\dashboard\PanelBox;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $from string */
/* @var $to string */

/* @var $pendingRepairRequests RepairRequest[] */
/* @var $completedRepairRequests RepairRequest[] */
/* @var $departedRepairRequests RepairRequest[] */

$todaysServices = [];
$upcomingDays = [];

$this->title = 'Works Dashboard';

?>
<div class="row" style="margin:5px;">
    <div class="col-md-12">
        <?php
        $panel = PanelBox::begin([
            'title' => $this->title,
            'icon' => 'dashboard',
            //'body' => false,
            'color' => PanelBox::COLOR_BLUE
        ]);
        ?>
        <?php if (P::c(P::REPAIR_REPAIR_DASHBOARD_PAGE_NEW)) {
            $panel->addButton(Yii::t('app', 'New'), ['repair-request/create'], ['class' => 'btn btn-primary btn-flat']);
        }
        ?>
        <!--        -->
        <?php //$panel->beginHeaderItem() 
        ?>
        <!--        -->
        <?php //Html::beginForm(['index'], 'GET') 
        ?>
        <!--        -->
        <?php //DateRangeFilter::widget(['auto_submit' => true]) 
        ?>
        <!--        -->
        <?php //Html::endForm() 
        ?>
        <!--        -->
        <?php //$panel->endHeaderItem() 
        ?>

        <?= Html::beginForm(['site/works-dashboard'], 'GET', ['class' => 'form-inline']) ?>

        <div class="form-group">
            <label for="filterID">Filter By </label>
        </div>
        <?php $searchModel = new RepairRequestSearch(); ?>
        <!-- <div class="form-group">
            <label for="filterSector">Sectors</label>
        </div>
        <div class="form-group" style="width:200px">
            <//?= Select2::widget([
                'name'          => 'sector_id',
                'value'         => Yii::$app->request->get("sector_id"),
                'data'          => Admin::sectorsKeyValList(),
                'pluginOptions' => [
                    'multiple'   => true,
                    'allowClear' => true
                ],
                'options'       => [
                    'placeholder' => 'Sectors',
                ],
            ]) ?>
        </div> -->

        <div class="form-group" style="width:200px">
            <?= Select2::widget([
                'name' => 'technician_id',
                'value' => Yii::$app->request->get("technician_id"),
                'data' => Technician::getTechnicianByDivision(),
                'pluginOptions' => [
                    'multiple' => true,
                    'allowClear' => true
                ],
                'options' => [
                    'placeholder' => 'Technicians'
                ],
            ]) ?>
        </div>
        <?php
        $model = new RepairRequest();
        $list = $model->service_type_list;
        $loggedInDivision = Account::getAdminDivisionID();
        $out = [];

        switch ($loggedInDivision) {
            case Division::DIVISION_VILLA:
                $out[] = ["id" => RepairRequest::TYPE_BREAKDOWN, "value" => $list[RepairRequest::TYPE_BREAKDOWN]];
                $out[] = ["id" => RepairRequest::TYPE_SCHEDULED_WORK, "value" => $list[RepairRequest::TYPE_SCHEDULED_WORK]];
                $out[] = ["id" => RepairRequest::TYPE_WORK, "value" => $list[RepairRequest::TYPE_WORK]];
                $out[] = ["id" => RepairRequest::TYPE_PPM, "value" => $list[RepairRequest::TYPE_PPM]];
                break;
            case Division::DIVISION_MALL:
                $out[] = ["id" => RepairRequest::TYPE_CORRECTIVE, "value" => $list[RepairRequest::TYPE_CORRECTIVE]];
                $out[] = ["id" => RepairRequest::TYPE_REACTIVE, "value" => $list[RepairRequest::TYPE_REACTIVE]];
                $out[] = ["id" => RepairRequest::TYPE_SCHEDULED_WORK, "value" => $list[RepairRequest::TYPE_SCHEDULED_WORK]];
                $out[] = ["id" => RepairRequest::TYPE_PPM, "value" => $list[RepairRequest::TYPE_PPM]];
                break;
            case Division::DIVISION_PLANT:
                $out[] = ["id" => RepairRequest::TYPE_BREAKDOWN, "value" => $list[RepairRequest::TYPE_BREAKDOWN]];
                $out[] = ["id" => RepairRequest::TYPE_PPM, "value" => $list[RepairRequest::TYPE_PPM]];
                break;
            default:
                $out = $list;
                break;
        }
        $data = ArrayHelper::map($out, 'id', 'value');


        ?>
        <div class="form-group" style="width:200px">
            <?= Select2::widget([
                'name' => 'service_type',
                'value' => Yii::$app->request->get("service_type"),
                'data' => ($loggedInDivision == '') ? $list : $data,
                'pluginOptions' => [
                    'allowClear' => true
                ],
                'options' => [
                    'placeholder' => 'Service Type'
                ],
            ]) ?>
        </div>

        <?= Html::submitButton("Filter", ['class' => 'btn btn-flat btn-default']) ?>
        <?= Html::a("Reset", ['site/works-dashboard'], ['class' => 'btn btn-flat btn-danger']) ?>
        <?= Html::endForm() ?> <?php PanelBox::end() ?> <?php
                                                        $panel = PanelBox::begin([
                                                            'title' => "Pending Services",
                                                            'icon' => 'dashboard',
                                                            'color' => PanelBox::COLOR_RED,
                                                            'canMinimize' => true,
                                                            'panelClass' => 'box box-danger box-solid',
                                                        ]);
                                                        ?>
        <?php if (P::c(P::REPAIR_REPAIR_DASHBOARD_PENDING_SERVICES_VIEW)) : ?>
        <?php $pendingRepairRequests->sort = false; ?>
        <?= GridView::widget([

                'dataProvider' => $pendingRepairRequests,
                'filterModel' => $pendingSearchModel,

                'rowOptions' => function (RepairRequest $model, $key, $index, $column) {
                    if (!empty($model->pending_equipment_id)) {
                        return ['class' => 'warning'];
                    }
                    // if ($model->hasWarning()) {
                    //     return ['class' => 'danger'];
                    // }
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
                        'enableSorting' => false,
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
                        'data' => ArrayHelper::map(Division::find()->all(), 'id', 'name'),
                        'filter' => Html::activeDropDownList(
                            $pendingSearchModel,
                            'division_id',
                            ArrayHelper::map(Division::find()->all(), 'id', 'name'),
                            ['class' => 'form-control', 'prompt' => 'Select Division']
                        ),

                    ],
                    [
                        'class' => RelationColumn::className(),

                        'attribute' => 'location_id',
                        'value' => function ($model) {

                            if (!empty($model->location_id))
                                return $model->location->name;
                        },
                        'data' => ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')


                    ],
                    // 'urgent_status',

                    [
                        'attribute' => 'repair_request_path',
                        'value' => function ($model) {
                            return $model->repair_request_path;
                        },
                    ],
                    // 'project_id',
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
                    'service_note',
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
                        'class'     => RelationColumn::className(),
                        'attribute' => 'completed_by',
                        'label'     => 'Completed By',
                        'value'     => function ($model) {
                            return $model->getBlamable($model->completed_by);
                        },
                        'data' => array_merge(
                            ['Admins' =>  ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                            ['Tehnicians' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                        ),

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
                                return Html::a(Yii::t("app", 'View'), ['/repair-request/view', 'id' => $model->id], $options);
                            }
                        ]
                    ],
                ],
            ]); ?> <?php endif; ?>
        <?php PanelBox::end() ?> <?php
                                    $panel = PanelBox::begin([
                                        'title' => "Ongoing Services",
                                        'icon' => 'dashboard',
                                        'color' => PanelBox::COLOR_ORANGE,
                                        'canMinimize' => true,
                                    ]);
                                    ?>
        <?php if (P::c(P::REPAIR_REPAIR_DASHBOARD_ONGOING_SERVICES_VIEW)) : ?>
        <?php $ongoingServices->sort = false; ?>

        <?= GridView::widget([
                'dataProvider' =>  $ongoingServices,
                'filterModel' => $ongoingSearchModel,
                'rowOptions' => function (RepairRequest $model, $key, $index, $column) {
                    if (!empty($model->pending_equipment_id)) {
                        return ['class' => 'warning'];
                    }
                    // if ($model->hasWarning()) {
                    //     return ['class' => 'danger'];
                    // }
                },
                'tableOptions' => ['class' => 'table table-bordered'],
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
                        'data' => ArrayHelper::map(Division::find()->all(), 'id', 'name'),
                        'filter' => Html::activeDropDownList(
                            $ongoingSearchModel,
                            'division_id',
                            ArrayHelper::map(Division::find()->all(), 'id', 'name'),
                            ['class' => 'form-control', 'prompt' => 'Select Division']
                        ),

                    ],
                    [
                        'class' => RelationColumn::className(),

                        'attribute' => 'location_id',
                        'value' => function ($model) {

                            if (!empty($model->location_id))
                                return $model->location->name;
                        },
                        'data' => ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')


                    ],
                    // 'urgent_status',

                    [
                        'attribute' => 'repair_request_path',
                        'value' => function ($model) {
                            return $model->repair_request_path;
                        },
                    ],
                    // 'project_id',
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
                    'service_note',
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
                        'class'     => RelationColumn::className(),
                        'attribute' => 'completed_by',
                        'label'     => 'Completed By',
                        'value'     => function ($model) {
                            return $model->getBlamable($model->completed_by);
                        },
                        'data' => array_merge(
                            ['Admins' =>  ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                            ['Tehnicians' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                        ),

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
                                return Html::a(Yii::t("app", 'View'), ['/repair-request/view', 'id' => $model->id], $options);
                            }
                        ]
                    ],
                ],
            ]); ?> <?php endif; ?>
        <?php PanelBox::end() ?> <?php
                                    $panel = PanelBox::begin([
                                        'title' => "Departed works services",
                                        'icon' => 'dashboard',
                                        'color' => PanelBox::COLOR_GREEN,
                                        'canMinimize' => true,
                                        'panelClass' => 'box box-success box-solid  ',
                                    ]);
                                    ?>
        <?php if (P::c(P::REPAIR_REPAIR_DASHBOARD_DEPARTED_SERVICES_VIEW)) : ?>
        <?php $departedRepairRequests->sort = false; ?>
        <?= GridView::widget([
                'dataProvider' => $departedRepairRequests,
                'filterModel' => $departedSearchModel,
                'rowOptions' => function (RepairRequest $model, $key, $index, $column) {
                    if (!empty($model->pending_equipment_id)) {
                        return ['class' => 'warning'];
                    }
                    // if ($model->hasWarning()) {
                    //     return ['class' => 'danger'];
                    // }
                },
                'tableOptions' => ['class' => 'table table-bordered'],
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
                        'data' => ArrayHelper::map(Division::find()->all(), 'id', 'name'),
                        'filter' => Html::activeDropDownList(
                            $departedSearchModel,
                            'division_id',
                            ArrayHelper::map(Division::find()->all(), 'id', 'name'),
                            ['class' => 'form-control', 'prompt' => 'Select Division']
                        ),

                    ],
                    [
                        'class' => RelationColumn::className(),

                        'attribute' => 'location_id',
                        'value' => function ($model) {

                            if (!empty($model->location_id))
                                return $model->location->name;
                        },
                        'data' => ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')


                    ],
                    // 'urgent_status',

                    [
                        'attribute' => 'repair_request_path',
                        'value' => function ($model) {
                            return $model->repair_request_path;
                        },
                    ],
                    // 'project_id',
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
                    'service_note',
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
                            ['Technicians' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                        ),

                    ],
                    [
                        'attribute' =>  'completed_at',
                        'class' => common\components\extensions\DateColumn::class,
                    ],
                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'completed_by',
                        'label'     => 'Completed By',
                        'value'     => function ($model) {
                            return $model->getBlamable($model->completed_by);
                        },
                        'data' => array_merge(
                            ['Admins' =>  ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                            ['Tehnicians' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                        ),

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
                                return Html::a(Yii::t("app", 'View'), ['/repair-request/view', 'id' => $model->id], $options);
                            }
                        ]
                    ],
                ],
            ]); ?> <?php endif; ?>
        <?php PanelBox::end() ?> <?php
                                    $panel = PanelBox::begin([
                                        'title' => "Upcoming Days Services",
                                        'icon' => 'dashboard',
                                        'color' => PanelBox::COLOR_BLUE,
                                        'canMinimize' => true,
                                        // 'panelClass' => 'box box-info box-solid  ',
                                    ]);
                                    ?>
        <?php if (P::c(P::REPAIR_REPAIR_DASHBOARD_UPCOMING_DAYS_SERVICES_VIEW)) : ?>
        <?php $upcoming_days_services->sort = false; ?>

        <?= GridView::widget([
                'dataProvider' => $upcoming_days_services,
                'filterModel' => $upcomingSearchModel,
                'rowOptions' => function (RepairRequest $model, $key, $index, $column) {
                    if (!empty($model->pending_equipment_id)) {
                        return ['class' => 'warning'];
                    }
                    // if ($model->hasWarning()) {
                    //     return ['class' => 'danger'];
                    // }
                },
                'tableOptions' => ['class' => 'table table-bordered'],
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
                        'data' => ArrayHelper::map(Division::find()->all(), 'id', 'name'),
                        'filter' => Html::activeDropDownList(
                            $upcomingSearchModel,
                            'division_id',
                            ArrayHelper::map(Division::find()->all(), 'id', 'name'),
                            ['class' => 'form-control', 'prompt' => 'Select Division']
                        ),

                    ],
                    [
                        'class' => RelationColumn::className(),

                        'attribute' => 'location_id',
                        'value' => function ($model) {

                            if (!empty($model->location_id))
                                return $model->location->name;
                        },
                        'data' => ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')


                    ],
                    // 'urgent_status',

                    [
                        'attribute' => 'repair_request_path',
                        'value' => function ($model) {
                            return $model->repair_request_path;
                        },
                    ],
                    // 'project_id',
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
                    'service_note',
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
                        'class'     => RelationColumn::className(),
                        'attribute' => 'completed_by',
                        'label'     => 'Completed By',
                        'value'     => function ($model) {
                            return $model->getBlamable($model->completed_by);
                        },
                        'data' => array_merge(
                            ['Admins' =>  ArrayHelper::map(Admin::find()->all(), 'id', 'name')],
                            ['Tehnicians' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                        ),

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
                                return Html::a(Yii::t("app", 'View'), ['/repair-request/view', 'id' => $model->id], $options);
                            }
                        ]
                    ],
                ],
            ]); ?> <?php endif; ?>
        <?php PanelBox::end() ?>
    </div>
</div>

<style type="text/css">
<?php ob_start() ?>.content-header {
    display: none;
}

.content-wrapper {
    position: relative;
}

.content {
    padding: 0;
}

<?php $css=ob_get_clean() ?><?php $this->registerCss($css) ?>
</style>