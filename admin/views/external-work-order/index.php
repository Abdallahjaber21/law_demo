<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\OptionsColumn;
use common\config\includes\P;
use common\models\RepairRequest;
use common\models\Division;
use common\models\search\RepairRequestSearch;
use yii\data\ArrayDataProvider;
use common\components\extensions\RelationColumn;
use common\models\Admin;
use common\models\Location;
use common\models\Technician;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


$this->title = 'External Work Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="repair-request-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_LIGHTBLUE
            ]);

            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,

                'rowOptions'   => function (RepairRequest $model, $key, $index, $column) {
                    if (!empty($model->pending_equipment_id)) {
                        return ['class' => 'warning'];
                    }
                },
                'tableOptions' => ['class' => 'table table-bordered'],
                'rowOptions'   => function ($model, $key, $index, $column) {
                    if ($model->urgent_status == true) {
                        return ['class' => 'danger'];
                    }
                },
                'columns'      => [
                    'id',
                    // 'technician_id',
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
                        'data' => ArrayHelpeR::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),


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
                        'attribute' =>   'repair_request_path',
                        'value' => function ($model) {
                            return $model->repair_request_path;
                        },
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' =>  'owner_id',
                        'value' => function ($model) {
                            if (!empty($model->owner_id))
                                return $model->owner->name;
                        },
                        'data' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),

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
                        'attribute' =>  'team_leader_id',
                        'value' => function ($model) {

                            if (!empty($model->team_leader_id))
                                return $model->teamLeader->name;
                        },
                        'data' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),

                    ],
                    'service_note',
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
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
                            ['Technicians' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                        ),

                    ],


                    [
                        'class'         => ActionColumn::className(),
                        'template'      => '{update}',
                        'headerOptions' => ['style' => 'width:33px;white-space: nowrap;'],
                        'visibleButtons' => [
                            'update' => function ($model) {
                                return $model->status != RepairRequest::STATUS_COMPLETED;
                            },
                        ],
                        'permissions' => [
                            'update' => P::REPAIR_EXTERNAL_WORK_ORDER_PAGE_UPDATE,


                        ],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>