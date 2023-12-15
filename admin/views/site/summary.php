<?php

use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\components\extensions\Select2;
use common\config\includes\P;
use common\models\Account;
use common\models\Assignee;
use common\models\Division;
use common\models\Employee;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\MainSector;
use common\models\Profession;
use common\models\RepairRequest;
use common\models\Route;
use common\models\RouteAssignment;
use common\models\search\AssigneeSearch;
use common\models\search\TechnicianSearch;
use common\models\Sector;
use common\models\Technician;
use common\models\Truck;
use common\models\users\Admin;
use common\widgets\dashboard\PanelBox;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;


$todaysServices = [];
$upcomingDays = [];

$this->title = 'Summary Dashboard';
?>
<div class="row" style="margin: 0; padding-top: 2rem;">
    <div class="col-md-12">


        <?= Html::beginForm(['site/works-dashboard'], 'GET', ['class' => 'form-inline']) ?>
        <?= Html::endForm() ?>

        <?php
        $panel = PanelBox::begin([
            'title' => 'OverDue Work Orders',
            'icon' => 'dashboard',
            'canMinimize' => true,
            'panelClass' => 'box box-danger box-solid  ',
        ]);
        ?>
        <?php $overdue->sort = false; ?>

        <?= GridView::widget([
            'dataProvider' => $overdue,
            'filterModel' => $overdueSearchModel,
            'id' => 'repair-requests-grid1',

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
                    'attribute' => 'days_Overdue',
                    'enableSorting' => true,
                    'value' => function ($model) {
                        $createdAtDate = date('Y-m-d', strtotime($model->created_at));
                        $nowDate = date('Y-m-d');
                        $differenceInDays = strtotime($nowDate) - strtotime($createdAtDate);
                        $differenceInDays = floor($differenceInDays / (60 * 60 * 24));
                        return $differenceInDays + 1;
                    }



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
                        $overdueSearchModel,
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
                        ['Admins' =>  ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_DESC])->all(), 'id', 'name')],
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
                    ],
                    'permissions' => [
                        'view' => P::REPAIR_REPAIR_DASHBOARD_PAGE_VIEW
                    ]
                ],
            ],
        ]); ?>
        <?php PanelBox::end() ?>
        <?php
        $panel = PanelBox::begin([
            'title' => $this->title,
            'icon' => 'calendar',
            'color' => PanelBox::COLOR_BLUE,
            'canMinimize' => true,
            'panelClass' => 'box box-primary box-solid  ',
        ]);
        ?>
        <?php $repairRequests->sort = false; ?>

        <?= GridView::widget([
            'dataProvider' => $repairRequests,
            'filterModel' => $repairSearchModel,
            'id' => 'repair-requests-grid', // Set a unique ID for this GridView
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
                    'format' => 'raw',
                    'headerOptions' => ['id' => 'service-type-header'], // Set a unique ID for this column
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
                        $repairSearchModel,
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
                    ],
                    'permissions' => [
                        'view' => P::REPAIR_REPAIR_DASHBOARD_PAGE_VIEW
                    ]
                ],
            ],
        ]); ?>
        <?php PanelBox::end() ?>
        <?php
        $panel = PanelBox::begin([
            'title' => 'Technicians',
            'icon' => 'users',
            'color' => PanelBox::COLOR_BLUE,
            'canMinimize' => true,
            'panelClass' => 'box box-success box-solid  ',
        ]);
        ?>
        <?php
        $model = new RepairRequest();
        $list = $model->service_type_list; ?>
        <?= GridView::widget([
            // 'dataProvider' => new ArrayDataProvider(['allModels' => $technicians]),
            'dataProvider' => $technicians,
            'tableOptions' => ['class' => 'table table-bordered'],
            'filterModel' => $searchModel,
            'columns' => [

                [
                    'attribute' => 'technician_name',
                    'label' => 'Name',
                    'value' => function ($model) {
                        if (!empty($model->user->name)) {
                            return $model->user->name;
                        }
                        return null;
                    },

                ],

                [
                    'class' => RelationColumn::className(),
                    'attribute' => 'main_sector_id',
                    'label' => 'Main Sector',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if (!empty($model->user->mainSector)) {
                            $link = ['main-sector/view', 'id' => $model->user->main_sector_id];
                            return Html::a($model->user->mainSector->name, $link);
                        }
                        return null;
                    },
                    'data' => ArrayHelper::map(MainSector::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')


                ],
                [
                    'class' => RelationColumn::className(),
                    'attribute' => 'profession_id',
                    'label' => 'Profession',
                    'format' => 'html',
                    'value' => function ($model) {
                        if (!empty($model->user->profession->name)) {
                            $link = ['profession/view', 'id' => $model->user->profession_id];
                            return Html::a($model->user->profession->name, $link);
                        }
                        return null;
                    },
                    'data' => ArrayHelper::map(Profession::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                ],

                [
                    'attribute' => 'account_id',
                    'label' => 'Technician Type',
                    'value' => function ($model) {
                        return @$model->user->account->type0->label;
                    },
                    'filter'    => Select2::widget([
                        'model'         => $searchModel,
                        'attribute'     => 'account_id',
                        'data'          => Account::getTechnicianOptions(),
                        'pluginOptions' => [
                            'multiple'   => false,
                            'allowClear' => true
                        ],
                        'options'       => [
                            'placeholder' => ''
                        ],
                    ])

                ],
                [
                    'attribute' => 'badge_number',
                    'value' => function ($model) {
                        if (!empty($model->user->badge_number)) {
                            return ($model->user->badge_number);
                        }
                        return null;
                    },
                ],
                [
                    'attribute' => 'status',
                    'label' => 'Work Status',
                    'enableSorting' => false,
                    'value' => function ($model) {
                        return $model->user->getTechnicianWorkStatus(true);
                    },
                    'class' => common\components\extensions\OptionsColumn::class,

                ],

                [
                    'attribute' => 'repair_request_id',
                    'label' => 'Work Orders Id',
                    'format' => 'raw',
                    'enableSorting' => false,
                    'value' => function ($model) {
                        if (!empty($model->profession->name)) {
                            $link = ['profession/view', 'id' => $model->user->profession_id];
                            return Html::a($model->user->profession->name, $link);
                        }
                        $link = ['repair-request/view', 'id' => $model['user']['latestAssignee']['repairRequest']['id']];
                        return Html::a($model->repair_request_id, $link);

                        return $model['latestAssignee']['repairRequest']['id'] ?? null;
                    },
                ],
                [
                    'class' => RelationColumn::className(),
                    'attribute' => 'work_order_type',
                    'label' => 'Work Orders Type',
                    'value' => function ($model) {
                        return $model['user']['latestAssignee']['repairRequest']['service_type_label'] ?? null;
                    },

                    'data' => $list,


                ],



            ],
        ]); ?>
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

    <?php $css = ob_get_clean() ?><?php $this->registerCss($css) ?>
</style>