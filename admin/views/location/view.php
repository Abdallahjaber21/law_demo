<?php

use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Account;
use common\models\Admin;
use common\models\Category;
use common\models\Division;
use common\models\Equipment;
use common\models\EquipmentType;
use common\models\LocationEquipments;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use common\widgets\inputs\LocationPicker;
use kartik\select2\Select2;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$equipments = $model->locationEquipments;

$new_data_provider = new ArrayDataProvider(
    [
        'allModels' => $equipments,
    ]
);
$pageId = Yii::$app->controller->id;
$modelname = Location::className();
$attributes = common\models\Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
?>
<div class="location-view">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::MANAGEMENT_LOCATION_PAGE_UPDATE)) { ?>
            <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'visible' => !in_array('id', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'name',
                        'visible' => !in_array('name', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'division_id',
                        'visible' => !in_array('division_id', $hiddenAttributes),
                        'value' => Html::tag("p", @$model->division->name),
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'sector_id',
                        'visible' => !in_array('sector_id', $hiddenAttributes),
                        'value' => Html::tag("p", $model->sector->name),
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'code',
                        'visible' => !in_array('code', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'status',
                        'visible' => !in_array('status', $hiddenAttributes),
                        'value' => $model->status_label
                    ],
                    [
                        'attribute' => 'country_id',
                        'value' => Html::tag("p", @$model->sector->country->name),
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'state_id',
                        'value' => Html::tag("p", @$model->sector->state->name),
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'city_id',
                        'value' => Html::tag("p", @$model->sector->city->name),
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'address',
                        'visible' => !in_array('address', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'map',
                        'visible' => !in_array('longitude', $hiddenAttributes) || !in_array('latitude', $hiddenAttributes),
                        'value' => function ($model) {

                            if (!empty($model->latitude) && !empty($model->longitude)) {
                                return LocationPicker::widget([
                                    'address_attr' => "map-address",
                                    'latitude_attr' => Html::getInputId($model, 'latitude'),
                                    'longitude_attr' => Html::getInputId($model, 'longitude'),
                                    'latitude' => $model->latitude,
                                    'longitude' => $model->longitude,
                                    'view_only' => true
                                ]);
                            }
                        },
                        'itemOptions' => ['class' => 'col-photo col-md-3 col-sm-4 col-xs-6'],
                        'format' => 'raw'
                    ],

                    [
                        'attribute' => 'created_at',
                        'visible' => !in_array('created_at', $hiddenAttributes),
                    ],
                    'updated_at',
                    [
                        'attribute' => 'expiry_date',
                        'visible' => !in_array('expiry_date', $hiddenAttributes) && ($model->division_id == Division::DIVISION_VILLA),
                    ],
                    [
                        'attribute' => 'created_by',
                        'value' => function (Location $model) {

                            if (!empty($model->created_by)) {
                                $account = Account::findOne($model->created_by);
                                if (!empty($account)) {
                                    $admin = Admin::find()->where(['id' => $model->created_by])->one();
                                    if (!empty($admin)) {

                                        return ($admin->name);
                                    }
                                }
                            }
                        },
                        'data' =>
                        ArrayHelper::map(Admin::find()->all(), 'id', 'name')


                    ],
                    [
                        'attribute' => 'updated_by',
                        'value' => function (Location $model) {

                            if (!empty($model->updated_by)) {
                                $account = Account::findOne($model->updated_by);
                                if (!empty($account)) {
                                    $admin = Admin::find()->where(['id' => $model->updated_by])->one();
                                    if (!empty($admin)) {
                                        return ($admin->name);
                                    }
                                }
                            }
                        },
                        'data' => ArrayHelper::map(Admin::find()->all(), 'id', 'name')


                    ],
                    [
                        'attribute' => 'owner',
                        'visible' => Account::getAdminDivisionID() == Division::DIVISION_VILLA && !in_array('owner', $hiddenAttributes),
                        'value' => function ($model) {
                            return $model->owner;
                        }
                    ],
                    [
                        'attribute' => 'owner_phone',
                        'visible' => Account::getAdminDivisionID() == Division::DIVISION_VILLA && !in_array('owner_phone', $hiddenAttributes),
                        'value' => function ($model) {
                            return $model->owner_phone;
                        }
                    ],
                ],
            ]) ?>

            <?php PanelBox::end() ?>
        </div>

        <?php if (P::c(P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_VIEW)) : ?>
        <div class="col-md-12">
            <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode('Equipments'),
                    'icon' => 'eye',
                    'color' => PanelBox::COLOR_GRAY
                ]);
                ?>

            <?php if (P::c(P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['location-equipments/index', 'location_id' => $model->id], ['class' => 'btn btn-primary btn-flat', 'style' => 'margin-right:10px']);
                }
                ?>

            <?= GridView::widget([
                    'dataProvider' => $new_data_provider,
                    'filterModel' => null,
                    'columns' => [

                        [
                            'attribute' => 'name',
                            'value' => function ($model) {
                                return $model->equipment->name;
                            }
                        ],
                        'code',
                        [
                            'attribute' => 'category_id',
                            'format' => 'html',
                            'value' => function ($model) {
                                if (!empty($model->equipment->category)) {
                                    return $model->equipment->category->name;
                                }
                            },
                            'label' => 'Category'

                        ],
                        [
                            'attribute' => 'equipment_type_id',
                            'value' => function ($model) {
                                if ($model->equipment)
                                    return !empty($model->equipment->equipmentType->code) ? $model->equipment->equipmentType->code . ' - ' . $model->equipment->equipmentType->name : $model->equipment->equipmentType->name;
                            },
                            'label' => 'Equipment Type'

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
                            'attribute' => 'Action',
                            'value' => function ($model) {
                                return Html::a("Update", \yii\helpers\Url::to(['location-equipments/update', 'id' => $model->id]), ['class' => 'btn btn-xs btn-success']);
                            },
                            'format' => 'html'
                        ],
                    ],
                ]); ?>

            <?php PanelBox::end() ?>
        </div>
        <?php endif; ?>
    </div>
</div>