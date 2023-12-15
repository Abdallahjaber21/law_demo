<?php

use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\widgets\dashboard\PanelBox;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CoordinatesIssueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coordinates Issues';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coordinates-issue-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]); ?>
            <?php if (P::c(P::MANAGEMENT_COORDINATES_ISSUES_PAGE_EXPORT)) {
                $panel->addButton(
                    Yii::t('app', 'Export'),
                    array_merge(['export/coordinate-issues'], Yii::$app->request->get()),
                    [
                        'class' => 'btn btn-warning btn-flat',
                        'data-method' => 'POST',
                    ]
                );
            } ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    //                    [
                    //                        'class'         => 'yii\grid\SerialColumn',
                    //                        'headerOptions' => ['style' => 'width:50px'],
                    //                    ],
                    [
                        'attribute' => 'id',
                        'value' => function ($model) {
                            return Html::a($model->id, ['coordinates-issue/view', 'id' => $model->id]);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],
                    [
                        'attribute' => 'location_name',
                        'value' => 'location.name',
                        'format' => 'raw'
                    ],
                    [
                        'label' => 'Reported By',
                        'attribute' => 'reported_by_name',
                        'value' => 'reportedBy.name'
                    ],
                    'old_latitude',
                    'old_longitude',
                    'new_latitude',
                    'new_longitude',
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>