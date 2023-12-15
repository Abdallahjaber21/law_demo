<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\models\PlantPpmTasks;
use rmrevin\yii\fontawesome\FA;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\PlantPpmTasksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Plant Ppm Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plant-ppm-tasks-index">

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
            <?php if (P::c(P::PPM_PLANT_PPM_TASKS_NEW)) {
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
                        'attribute' => 'task_type',
                        'class' => common\components\extensions\OptionsColumn::class,
                    ],
                    'occurence_value',
                    [
                        'attribute' => 'meter_type',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
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
                            'update' => P::PPM_PLANT_PPM_TASKS_UPDATE,
                            'view' => P::PPM_PLANT_PPM_TASKS_VIEW,
                            'delete' => P::PPM_PLANT_PPM_TASKS_DELETE,
                        ],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>