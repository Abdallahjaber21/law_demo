<?php

use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Account;
use common\models\Admin;
use common\models\Division;
use common\models\Equipment;
use common\models\EquipmentCa;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use common\models\LocationEquipments;
use common\models\Location;
use common\models\PlantPpmTasks;
use common\models\search\MallPpmTasksSearch;
use common\models\search\PlantPpmTasksSearch;

/* @var $this yii\web\View */
/* @var $model common\models\Equipment */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$pageId = Yii::$app->controller->id;
$modelname = Equipment::className();
$attributes = common\models\Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
?>
<div class="equipment-view">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::MANAGEMENT_EQUIPMENT_PAGE_UPDATE)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?php $dynamicAttributes = [];

            foreach ($model->equipmentCaValues as $equipmentCaValue) {
                $attributeName = strtolower(str_replace(' ', '', $equipmentCaValue->equipmentCa->name));
                $dynamicAttributes[] = [
                    'label' => $equipmentCaValue->equipmentCa->name,
                    'format' => 'raw',
                    'value' => function ($model) use ($equipmentCaValue) {
                        return $equipmentCaValue->value;
                    },
                ];
            } ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => array_merge(
                    [
                        [
                            'attribute' => 'id',
                            'visible' => !in_array('id', $hiddenAttributes),
                        ],
                        [
                            'attribute' => 'name',
                            'visible' => !in_array('name', $hiddenAttributes),
                        ],
                        [
                            'attribute' => 'code',
                            'visible' => !in_array('code', $hiddenAttributes),
                        ],
                        // [
                        //     'attribute' => 'location_id',
                        //     'value'     => function ($model) {
                        //         if (!empty($model->location_id)) {
                        //             return "{$model->location->code} - {$model->location->name}";
                        //         }
                        //         return null;
                        //     },
                        //     'format'    => 'raw',
                        // ],
                        [
                            'attribute' => 'equipment_type_id',
                            'visible' => !in_array('equipment_type_id', $hiddenAttributes),
                            'value' => function ($model) {
                                if ($model->equipmentType)
                                    return !empty($model->equipmentType->code) ? $model->equipmentType->code . '-' . $model->equipmentType->name : $model->equipmentType->name;
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'division_id',
                            // 'contentOptions' => ['style' => empty(Account::getAdminAccountTypeDivisionModel()) ? 'display:block;' : 'display:none;'],
                            'value' => function ($model) {

                                if (!empty($model->division_id))
                                    return ($model->division->name);
                            },
                            'visible' => empty(Account::getAdminAccountTypeDivisionModel()) && !in_array('division_id', $hiddenAttributes),
                        ],
                        // [
                        //     'attribute' => 'equipment_path_id',
                        //     'value' => function ($model) {
                        //         return Equipment::getLayersValue($model->equipmentPath->value);
                        //     },
                        //     'format' => 'raw',
                        // ],
            

                        // [
                        //     'attribute' => 'equipmentCaValues',
                        //     'label' => 'Equipment Custom',
                        //     'format' => 'raw',
                        //     'value' => function ($model) {
                        //         $values = '';
                        //         foreach ($model->equipmentCaValues as $equipmentCaValue) {
                        //             $values .= $equipmentCaValue->equipmentCa->name . ': ' . $equipmentCaValue->value . '<br>';
                        //         }
                        //         return $values;
                        //     },
                        // ],
                        [
                            'attribute' => 'category_id',
                            'visible' => !in_array('category_id', $hiddenAttributes),
                            'format' => 'html',
                            'value' => function ($model) {
                                if (!empty($model->category)) {
                                    return $model->category->name;
                                }
                            },
                        ],

                        [
                            'attribute' => 'status',
                            'visible' => !in_array('status', $hiddenAttributes),
                            'value' => $model->status_label
                        ],

                        'description:ntext',
                        [
                            'attribute' => 'created_at',
                            'format' => 'datetime',
                            'visible' => !in_array('created_at', $hiddenAttributes),
                        ],
                        'updated_at:datetime',
                        [
                            'attribute' => 'created_by',
                            'label' => 'Created By',
                            'value' => function ($model) {
                                return Admin::findOne($model->created_by)->name;
                            },

                        ],
                        [
                            'attribute' => 'updated_by',
                            'label' => 'Updated By',
                            'value' => function ($model) {
                                return Admin::findOne($model->created_by)->name;
                            },

                        ],

                    ],
                ),
            ]) ?>

            <?php PanelBox::end() ?>
        </div>
        <?php
        $dataprovider = new ArrayDataProvider(
            [
                'allModels' => $model->locationEquipments,
            ]
        ); ?>
        <?php if (P::c(P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_VIEW)): ?>
            <?php
            $equipment = Equipment::findOne($model->id);
            $division_id = $equipment->division_id; ?>
            <div class="col-md-12">
                <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode('Location Equipments'),
                    'icon' => 'eye',
                    'color' => PanelBox::COLOR_GREEN
                ]);
                ?>

                <?php
                if (!empty($model->locationEquipments)):

                    ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataprovider,
                        'filterModel' => null,
                        'columns' => [
                            [
                                'attribute' => 'code',
                                'value' => function ($model) {
                                    return $model->code;
                                }
                            ],
                            [
                                'attribute' => 'location_id',
                                'value' => function ($model) {
                                    return $model->location->name;
                                }
                            ],

                            [
                                'attribute' => 'status',
                                'class' => common\components\extensions\OptionsColumn::class
                            ],
                            [
                                'attribute' => 'value',
                                'value' => function ($model) {
                                    return Equipment::getLayersValue($model->value);
                                },
                                'format' => 'raw',
                                'filter' => false
                            ],
                            [
                                'attribute' => 'custom attributes',
                                'visible' => EquipmentCa::getEquipmentCustomAttributeDivisionCount(@$equipment->division_id) > 0,
                                'value' => function ($model) use ($dynamicAttributes) {
                                    return Equipment::getEquipmentCustomAttributes($model->equipment_id, $model->id);
                                }
                            ],
                            [
                                'attribute' => 'Action',
                                'value' => function ($model) {
                                    return Html::a("Update", \yii\helpers\Url::to(['location-equipments/update', 'id' => $model->id]), ['class' => 'btn btn-xs btn-success']);
                                },
                                'format' => 'html'
                            ],
                        ],
                    ]); ?>
                <?php else: ?>
                    <h3 class="no-data">No Data</h3>
                <?php endif; ?>
                <?php PanelBox::end() ?>
            </div>
        <?php endif; ?>
        <?php if (P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_VIEW)): ?>
            <?php if ($model->division_id == Division::DIVISION_MALL || $model->division_id == Division::DIVISION_PLANT): ?>
                <div class="col-md-12">
                    <?php if ($model->division_id == Division::DIVISION_MALL) {
                        $title = 'Mall PPM Tasks';
                        $dataprovidertask = new ArrayDataProvider(
                            [
                                'allModels' => $model->equipmentType->mallPpmTasks,
                            ]
                        );
                        $task = 10;
                    } else {
                        $title = 'Plant PPM Tasks';
                        $searchModel = new PlantPpmTasksSearch();
                        $dataprovidertask = $searchModel->search(Yii::$app->request->queryParams);
                        $task = 20;
                    }
                    ?>
                    <?php
                    $panel = PanelBox::begin([
                        'title' => Html::encode($title),
                        'icon' => 'eye',
                        'color' => PanelBox::COLOR_ORANGE
                    ]);
                    ?>

                    <?php if (!empty($model->equipmentType)): ?>
                        <?= GridView::widget([
                            'dataProvider' => $dataprovidertask,
                            'filterModel' => 'null',
                            'columns' => [
                                [
                                    'attribute' => 'name',
                                    'enableSorting' => false,
                                    'value' => function ($model) {
                                            return $model->name;
                                        }
                                ],
                                ($task == 10) ?

                                [
                                    'attribute' => 'frequency',
                                    'enableSorting' => false,
                                    'value' => function ($model) {
                                            return $model->frequency_label;
                                        },
                                ] :
                                [
                                    'attribute' => 'task_type',
                                    'enableSorting' => false,
                                    'value' => function ($model) {
                                            return $model->task_type_label;
                                        },
                                ],


                                [
                                    'attribute' => 'occurence_value',
                                    'enableSorting' => false,
                                    'value' => function ($model) {
                                            return $model->occurence_value;
                                        },
                                    'format' => 'raw',
                                    'filter' => false
                                ],
                                ($task == 20) ?
                                [
                                    'attribute' => 'meter_type',
                                    'enableSorting' => false,
                                    'value' => function ($model) {
                                            return $model->meter_type_label;
                                        },
                                ] : [
                                    'attribute' => 'updated_at',
                                    'enableSorting' => false,


                                ],

                                [
                                    'attribute' => 'created_at',
                                    'enableSorting' => false,


                                ],
                            ],
                        ]); ?>
                    <?php else: ?>
                        <h3 class="no-data">No Data</h3>
                    <?php endif; ?>
                    <?php PanelBox::end() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<style>
    .grid-view tr td:last-child {
        text-align: left
    }
</style>