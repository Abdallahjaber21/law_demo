<?php

use common\models\EquipmentType;
use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\OptionsColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Admin;
use common\models\Division;
use common\models\Location;
use common\models\RepairRequest;
use common\models\Technician;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\RepairRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Plant PPM Services';
$this->params['breadcrumbs'][] = $this->title;

Yii::$app->formatter->timeZone = 'UTC';
?>
<div class="repair-request-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
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
                    // 'technician_id',
                    [
                        // 'class' => OptionsColumn::class,
                        'attribute' => 'status',
                        'value' => function ($model) {
                            return $model->getStatusTag();
                        },
                        'format' => 'html',
                        'contentOptions' => ['class' => 'td_tag'],
                        'class' => common\components\extensions\OptionsColumn::class,
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
                        'attribute' => 'meter_type',
                        'value' => function ($model) {
                            if (!empty($model->equipment_id)) {
                                $equipment = $model->equipment;
                                return @$equipment->equipment->equipmentType->meter_type_label;
                            }
                        },
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'name' => 'meter_type',
                            'attribute' => 'meter_type',
                            'data' => (new EquipmentType())->meter_type_list,
                            'pluginOptions' => [
                                'multiple' => false,
                                'allowClear' => true
                            ],
                            'options' => [
                                'placeholder' => 'Select Meter Type'
                            ],
                        ]),
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'meter_value',
                        'value' => function ($model) {
                            if (!empty($model->equipment_id)) {
                                $equipment = $model->equipment;
                                return @$equipment->meter_value;
                            }
                        },

                    ],
                    // [
                    //     'attribute' => 'division_id',
                    //     'value' => function ($model) {

                    //         if (!empty($model->division_id)) {
                    //             return $model->division->name;
                    //         }
                    //     },
                    //     'class' => common\components\extensions\RelationColumn::class,
                    //     'data' => (new Division())->name_list

                    // ],
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
                    [
                        'attribute' => 'created_at',
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
                            ['Admins' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                        ),

                    ],
                    [
                        'attribute' => 'completed_at',
                        'class' => common\components\extensions\DateColumn::class
                    ],
                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'completed_by',
                        'label'     => 'Completed By',
                        'value'     => function ($model) {
                            return $model->getBlamable($model->completed_by);
                        },
                        'data' => array_merge(
                            ['Admins' =>  ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_DESC])->all(), 'id', 'name')],
                            ['Admins' => ArrayHelper::map(Technician::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')],
                        ),

                    ],                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'buttons' => [
                            'view' => function ($url, $model) {
                                if (P::C(P::PPM_PLANT_PPM_SERVICES_VIEW)) {

                                    return Html::a('View', ['repair-request/view', 'id' => $model->id], ['class' => 'btn btn-xs btn-primary']);
                                }
                            },
                            'update' => function ($url, $model) {
                                if (P::C(P::PPM_PLANT_PPM_SERVICES_UPDATE)) {

                                    return Html::a('Update', ['repair-request/update', 'id' => $model->id], ['class' => 'btn  btn-xs btn-warning']);
                                }
                            },
                            'delete' => function ($url, $model) {
                                if (P::C(P::PPM_PLANT_PPM_SERVICES_DELETE)) {

                                    return Html::a('Delete', ['repair-request/delete', 'id' => $model->id], [
                                        'class' => 'btn btn-danger  btn-xs',
                                        'data' => [
                                            'confirm' => 'Are you sure you want to delete this item?',
                                            'method' => 'post',
                                        ],
                                    ]);
                                }
                            },
                        ],

                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>