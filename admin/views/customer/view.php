<?php

use common\components\extensions\RelationColumn;
use common\models\Customer;
use common\models\Location;
use common\models\Sector;
use common\widgets\dashboard\PanelBox;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Customer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-view">


    <div class="row">
        <div class="col-md-10">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon'  => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (\common\config\includes\P::c(\common\config\includes\P::MISC_MANAGE_CUSTOMERS)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?php if (\common\config\includes\P::c(\common\config\includes\P::MISC_MANAGE_CUSTOMERS)) { ?>
                <?php
                /*$panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-flat',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])*/
                ?>
            <?php } ?>    <?= DetailView::widget([
                'model'      => $model,
                'attributes' => [
                    'id',
                    'code',
                    'name',
                    'address',
                    'phone',
                    [
                        'attribute' => 'status',
                        'value'     => $model->status_label
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>

            <?php PanelBox::end() ?>
        </div>

        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => "Locations",
                'icon'  => 'table',
                'color' => PanelBox::COLOR_RED
            ]);
            ?>
            <?= GridView::widget([
                'dataProvider' => new ActiveDataProvider(['query' => $model->getLocations()]),
                'columns'      => [
                    [
                        'class'         => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    [
                        'attribute'     => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                        'value'         => function ($model) {
                            return Html::a($model->id, ['location/view', 'id' => $model->id]);
                        },
                        'format'        => 'raw',
                    ],
                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'sector_id',
                        'value'     => function ($model) {
                            if (!empty($model->sector_id)) {
                                return Html::a($model->sector->code, ['sector/view', 'id' => $model->sector_id]);
                            }
                            return null;
                        },
                        'format'    => 'raw',
                        'data'      => ArrayHelper::map(Sector::find()->all(), 'id', 'name')
                    ],
                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'customer_id',
                        'value'     => function ($model) {
                            if (!empty($model->customer_id)) {
                                return Html::a($model->customer->name, ['customer/view', 'id' => $model->customer_id]);
                            }
                            return null;
                        },
                        'format'    => 'raw',
                        'data'      => ArrayHelper::map(Customer::find()->all(), 'id', 'name')
                    ],
                    'code',
                    'name',
                    'address',
                    [
                        'attribute' => 'status',
                        'class'     => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'format'    => 'datetime',
                        'class'     => common\components\extensions\DateColumn::class
                    ],
//
//                    [
//                        'class' => ActionColumn::className(),
//                        'template' => '{view} {update} ',
//                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
//                    ],
                ],
            ]);
            ?>

            <?php PanelBox::end() ?>
        </div>

        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => "Equipments",
                'icon'  => 'table',
                'color' => PanelBox::COLOR_PURPLE
            ]);
            ?>
            <?= GridView::widget([
                'dataProvider' => new ActiveDataProvider(['query' => $model->getEquipments()]),
                'columns'      => [
                    [
                        'class'         => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    [
                        'attribute'     => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                        'value'         => function ($model) {
                            return Html::a($model->id, ['equipment/view', 'id' => $model->id]);
                        },
                        'format'        => 'raw',
                    ],

                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'location_id',
                        'value'     => function ($model) {
                            if (!empty($model->location_id)) {
                                return Html::a($model->location->name, ['location/view', 'id' => $model->location_id]);
                            }
                            return null;
                        },
                        'format'    => 'raw',
                        'data'      => ArrayHelper::map(Location::find()->all(), 'id', 'name')
                    ],
                    'code',
                    'name',
                    'contract_code',
                    [
                        'attribute' => 'status',
                        'class'     => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'equipment_type',
                        'class'     => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'manufacturer',
                        'class'     => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'unit_type',
                        'class'     => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'format'    => 'datetime',
                        'class'     => common\components\extensions\DateColumn::class
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
