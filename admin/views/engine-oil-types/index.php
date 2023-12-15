<?php

use common\components\extensions\OptionsColumn;
use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\config\includes\P;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EngineOilTypesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Engine Oil Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="engine-oil-types-index">

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
            <?php if (P::c(P::CONFIGURATIONS_ENGINE_OIL_PAGE_NEW)) {
                $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
            }
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                        'value' => function ($model) {
                            return Html::a($model->id, Url::to(['view', 'id' => $model->id]));
                        },
                        'format' => 'raw'
                    ],

                    'oil_viscosity',
                    [
                        'attribute' => 'motor_fuel_type_id',
                        'class' => OptionsColumn::class,
                    ],
                    'can_weight',
                    'oil_durability',
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
                            'view' => P::CONFIGURATIONS_ENGINE_OIL_PAGE_VIEW,
                            'update' => P::CONFIGURATIONS_ENGINE_OIL_PAGE_UPDATE,
                            'delete' =>  P::CONFIGURATIONS_ENGINE_OIL_PAGE_DELETE,

                        ],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>