<?php

use common\config\includes\P;
use common\models\Account;
use common\models\Admin;
use common\models\Division;
use common\models\Equipment;
use common\models\Location;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;

/* @var $this yii\web\View */
/* @var $model common\models\LocationEquipments */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Location Equipments', 'url' => ['index', 'location_id' => $model->location_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $locationId = $model->location->id;
$location = Location::findOne($locationId);
// print_r($location->division_id);
// exit;
?>
<div class="location-equipments-view">
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
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'equipment_name',
                        'label' => 'Equipment',
                        'value' => function ($model) {
                            return $model->equipment->name;
                        },
                    ],
                    [
                        'attribute' => 'location_id',
                        'value' => function ($model) {
                            return $model->location->name;
                        },
                        'filter' => false,
                    ],
                    'code',
                    [
                        'attribute' => 'value',
                        'value' => function ($model) {
                            return Equipment::getLayersValue($model->value);
                        },
                        'format' => 'raw',
                        'filter' => false
                    ],
                    [

                        'attribute' => 'safety_status',
                        'value' => function ($model) {
                            return $model->safety_status_label;
                        },
                        'visible' => $location->division_id === Division::DIVISION_PLANT,
                    ],
                    [

                        'attribute' => 'meter_value',
                        'value' => function ($model) {
                            return $model->meter_value;
                        },
                        'visible' => $location->division_id === Division::DIVISION_PLANT,
                    ],
                    [
                        'attribute' => 'meter_damaged',
                        'value' => function ($model) {
                            if ($model->meter_damaged === 0 || $model->meter_damaged === 1) {

                                $color = ($model->meter_damaged == 1) ? '#28a745;' : '#dc3545';
                                return Html::tag('div', ' ', ['style' => 'width:100px;height:20px;background-color:' . $color . '']);
                            }
                        },
                        'format' => 'raw',
                        'visible' => $location->division_id === Division::DIVISION_PLANT


                    ],
                    [
                        'attribute' => 'motor_fuel_type',
                        'value' => $model->motor_fuel_type_label,
                        'visible' => $location->division_id === Division::DIVISION_PLANT
                    ],
                    [
                        'attribute' => 'chassie_number',
                        'value' => function ($model) {
                            return $model->chassie_number;
                        },
                        'visible' => $location->division_id === Division::DIVISION_PLANT
                    ],
                    [
                        'attribute' => 'driver_id',
                        'visible' => ($location->division_id === Division::DIVISION_PLANT),
                        'value' => function ($model) {
                            return @$model->driver->name;
                        },
                        'filter' => false,
                    ],
                    'remarks',

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
                        'value' => $model->status_label
                    ],
                    [
                        'attribute' => 'updated_at',

                    ],
                    [
                        'attribute' => 'created_at',

                    ],
                    [
                        'attribute' => 'created_by',
                        'value' => function ($model) {
                            return Admin::findOne($model->created_by)->name;
                        }
                    ],
                    [
                        'attribute' => 'updated_by',
                        'value' => function ($model) {
                            return Admin::findOne($model->updated_by)->name;
                        }
                    ],
                ],
            ]) ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>