<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\OptionsColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Division;
use common\models\Equipment;
use common\models\EquipmentCa;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\Technician;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LocationEquipmentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Location Equipments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-equipments-index">

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
                // 'dataProvider' => new ActiveDataProvider(
                //     [
                //         'allModels' => $dataProvider,
                //     ]
                // ),
                // 'dataProvider' => new ActiveDataProvider(['query' => $dataProvider]),
            
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [

                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],

                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'division_id',
                        'value' => function ($model) {
                            return $model->division->name;
                        },
                        'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),

                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'location_id',
                        'value' => function ($model) {
                            return $model->location->name;
                        },
                        'filter' => false,
                        'data' => ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),

                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'equipment_id',
                        'value' => function ($model) {
                            return $model->equipment->name;
                        },
                        'data' => ArrayHelper::map(Equipment::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),

                    ],
                    [
                        'attribute' => 'driver_id',
                        'value' => function ($model) {
                            return @$model->driver->name;
                        },
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'attribute' => 'driver_id',

                            'data' => ArrayHelper::map(Technician::find()->andWhere(['IN', 'id', LocationEquipments::getDriverTechnicianId()])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                            'pluginOptions' => [
                                'multiple' => false,
                                'allowClear' => true
                            ],
                            'options' => [
                                'placeholder' => ''
                            ],
                        ])
                    ], 'code',
                    [

                        'attribute' => 'meter_value',
                        'value' => function ($model) {
                            return $model->meter_value;
                        },
                    ],
                    [
                        'attribute' => 'meter_damaged',
                        'value' => function ($model) {
                            if ($model->meter_damaged === 0 || $model->meter_damaged === 1) {
                                $color = ($model->meter_damaged == 1) ? '#28a745;' : '#dc3545';
                                return Html::tag('div', ' ', ['style' => 'width:100%;height:20px;background-color:' . $color . '']);
                            } else {
                                return null;
                            }
                        },
                        'format' => 'raw',
                        'filter' => Select2::widget([
                            'name' => 'meter_damaged',
                            'attribute' => 'meter_damaged',
                            'data' => [
                                '1' => 'Operational',
                                '0' => 'Damaged',
                            ],
                            'value' => Yii::$app->request->get('meter_damaged'),
                            'options' => [
                                'placeholder' => Yii::t("app", 'Meter Damaged'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]),
                    ],
                    [
                        'attribute' => 'motor_fuel_type',
                        'class' => OptionsColumn::class,
                    ],
                    [
                        'attribute' => 'chassie_number',
                        'value' => function ($model) {
                            return $model->chassie_number;
                        },
                    ],
                    [

                        'attribute' => 'safety_status',
                        'class' => common\components\extensions\OptionsColumn::class,
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
                        'attribute' => 'custom_attributes',
                        'label' => 'Attributes',
                        'value' => function ($model) {
                            return Equipment::getEquipmentCustomAttributes($model->equipment_id, $model->id);
                        },
                        'format' => 'raw',
                        'filter' => false
                    ],
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],

                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'permissions' => [
                            'update' => P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_UPDATE,
                            'view' => P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_VIEW,
                            'delete' => P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_DELETE,
                        ],
                    ],
                ],

            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>