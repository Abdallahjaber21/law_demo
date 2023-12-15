<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\models\Account;
use common\models\Division;
use common\models\Sector;
use common\models\SegmentPath;
use common\models\Technician;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SegmentPathSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Segment Paths';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="segment-path-index">

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
            <?php if (P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_NEW)) {
                $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
            }
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'value' => function ($model) {
                            return Html::a($model->id, Url::to(['view', 'id' => $model->id]));
                        },
                        'format' => 'raw',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],
                    'name',
                    'code',
                    [
                        'attribute' => 'value',
                        'value' => function ($model) {
                            return SegmentPath::getLayersValue($model->value);
                        },
                        'filter' => '',
                    ],
                    [
                        'attribute' =>  'division_id',
                        'value' => function ($model) {
                            if ($model->sector)
                                return $model->sector->mainSector->division->name;
                        },
                        'label' => 'Division',
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()),
                        'filter'    => Select2::widget([
                            'model'         => $searchModel,
                            'attribute'     => 'division_id',
                            'data'          => ArrayHelper::map(Division::find()->all(), 'id', function ($model) {
                                return "{$model->name}";
                            }),
                            'pluginOptions' => [
                                'multiple'   => false,
                                'allowClear' => true
                            ],
                            'options'       => [
                                'placeholder' => 'Select a Division'
                            ],
                        ])
                    ],
                    [
                        'attribute' =>  'sector_id',
                        'value' => function ($model) {
                            if ($model->sector)
                                $sectorLink = Html::a(
                                    (!empty($model->sector->code) ? "{$model->sector->code} - " : '') . "{$model->sector->name}",
                                    Url::to(['sector/view', 'id' => $model->sector_id])
                                );
                            return $sectorLink;
                        },
                        'format' => 'raw',
                        'filter'    => Select2::widget([
                            'model'         => $searchModel,
                            'attribute'     => 'sector_id',
                            'data'          => ArrayHelper::map(Technician::getTechnicianSectorsOptions(), 'id', function ($model) {
                                return implode(' - ', array_filter([$model->code, $model->name]));
                            }),
                            'pluginOptions' => [
                                'multiple'   => false,
                                'allowClear' => true
                            ],
                            'options'       => [
                                'placeholder' => 'Select Sector'
                            ],
                        ])
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
                    'description',
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete} {audit}',
                        'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                if (P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_DELETE)) {
                                    if ($model->status == SegmentPath::STATUS_DELETED) {
                                        return Html::a('Undelete', $url, [
                                            'title' => Yii::t('yii', 'Undelete'),
                                            'class' => 'btn btn-xs btn-warning',
                                            'style' => 'min-width:53px;',
                                            'data-method' => 'post',
                                            'data-confirm' => 'Are you sure you want to undelete this item?',
                                        ]);
                                    } else {
                                        $confirmMessage = 'Are you sure you want to delete this item?';
                                        return Html::a('Delete', $url, [
                                            'title' => Yii::t('yii', 'Delete'),
                                            'class' => 'btn btn-xs btn-danger',
                                            'style' => 'min-width:53px;',
                                            'data-method' => 'post',
                                            'data-confirm' => $confirmMessage,
                                        ]);
                                    }
                                }
                            },
                        ],
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'permissions' => [
                            'view' =>    P::MANAGEMENT_SEGMENT_PATH_PAGE_VIEW,
                            'update' =>  P::MANAGEMENT_SEGMENT_PATH_PAGE_UPDATE,
                            'enable' =>  P::MANAGEMENT_SEGMENT_PATH_PAGE_UPDATE,
                            'disable' => P::MANAGEMENT_SEGMENT_PATH_PAGE_UPDATE,
                            'delete' =>  P::MANAGEMENT_SEGMENT_PATH_PAGE_DELETE,
                            'audit' =>  P::MANAGEMENT_SEGMENT_PATH_PAGE_AUDIT,
                        ],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>