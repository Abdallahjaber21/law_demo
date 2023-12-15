<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\models\EquipmentCa;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Equipment;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EquipmentCaValueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equipment Ca Values';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-ca-value-index">
    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
            ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'equipment_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->equipment->name)) {
                                return $model->equipment->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Equipment::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'equipment_ca_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->equipmentCa->name)) {
                                return $model->equipmentCa->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(EquipmentCa::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],

                    'value',
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
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>