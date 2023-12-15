<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\EquipmentType;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MallPpmTasksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mall Ppm Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-ppm-tasks-index">

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
            <?php if (P::c(P::PPM_MALL_PPM_TASKS_NEW)) {
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
                        'format' => 'raw',
                    ],

                    'name',
                    [
                        'class' => common\components\extensions\OptionsColumn::class,
                        'attribute' => 'frequency',
                        'value' => function ($model) {
                            return $model->frequency_label;
                        }
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'equipment_type_id',
                        'value' => function ($model) {
                            return $model->equipmentType->name;
                        },
                        'data' => ArrayHelper::map(EquipmentType::find()->orderBy('name')->all(), 'id', 'name')
                    ],
                    'occurence_value',
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        // 'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],

                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'permissions' => [
                            'update' => P::PPM_MALL_PPM_TASKS_UPDATE,
                            'view' => P::PPM_MALL_PPM_TASKS_VIEW,
                            'delete' => P::PPM_MALL_PPM_TASKS_DELETE,
                        ],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>