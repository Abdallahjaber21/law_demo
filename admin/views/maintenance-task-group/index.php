<?php

use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\widgets\dashboard\PanelBox;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MaintenanceTaskGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Maintenance Task Groups');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="maintenance-task-group-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon'  => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::DEVELOPER)) {
                $panel->addButton(Yii::t('app', 'New'), ['manage'], ['class' => 'btn btn-primary btn-flat']);
            }
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'columns'      => [
                    [
                        'class'         => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    [
                        'attribute'     => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],

                    [
                        'attribute' => 'equipment_type',
                        'class'     => common\components\extensions\OptionsColumn::class
                    ],
                    'code',
                    'name',
                    'group_order',
                    [
                        'attribute' => 'status',
                        'class'     => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'format'    => 'datetime',
                        'class'     => common\components\extensions\DateColumn::class
                    ],

                    [
                        'class'         => ActionColumn::className(),
                        'template'      => '{view} {update} ',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
