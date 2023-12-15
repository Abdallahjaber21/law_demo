<?php

use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\widgets\dashboard\PanelBox;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EquipmentMaintenanceBarcodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'All Barcodes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-maintenance-barcode-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon'  => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'columns'      => [
                    //                    [
                    //                        'class'         => 'yii\grid\SerialColumn',
                    //                        'headerOptions' => ['style' => 'width:50px'],
                    //                    ],
                    //                    [
                    //                        'attribute'     => 'id',
                    //                        'headerOptions' => ['style' => 'width:75px'],
                    //                    ],
                    'barcode',
                    [
                        'label'     => 'Equipment',
                        'attribute' => 'equipment_search',
                        'value'     => function ($model) {
                            return Html::a($model->equipment->code, [
                                'equipment-maintenance-barcode/index', 'EquipmentMaintenanceBarcodeSearch[equipment_search]' => $model->equipment->code
                            ]);
                        },
                        'format'    => 'raw'
                    ],
                    [
                        'label'     => 'Manufacturer',
                        'attribute' => 'manufacturer_search',
                        'value'     => function ($model) {
                            return Html::a($model->equipment->manufacturer_label, [
                                'equipment-maintenance-barcode/index', 'EquipmentMaintenanceBarcodeSearch[manufacturer_search]' => $model->equipment->manufacturer
                            ]);
                        },
                        'filter'    => Html::activeDropDownList($searchModel, 'manufacturer_search', (new \common\models\Equipment())->manufacturer_list, [
                            'prompt' => 'Any',
                            'class'  => 'form-control'
                        ]),
                        'format'    => 'raw'
                    ],
                    'location',
                    //                    [
                    //                        'attribute' => 'status',
                    //                        'class'     => common\components\extensions\OptionsColumn::class
                    //                    ],
                    //                    [
                    //                        'attribute' => 'created_at',
                    //                        'format'    => 'datetime',
                    //                        'class'     => common\components\extensions\DateColumn::class
                    //                    ],
                    'code',

                    [
                        'class'          => ActionColumn::className(),
                        'template'       => '{delete}',
                        'visibleButtons' => [
                            'delete' => function ($model, $key, $index) {
                                return P::c(P::ALL_BARCODES_PAGE_DELETE);
                            }
                        ],
                        'headerOptions'  => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>