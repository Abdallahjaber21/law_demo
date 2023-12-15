<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\config\includes\P;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EquipmentPath */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equipment Paths';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-path-index">

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
            <?php if (P::c(P::MANAGEMENT_EQUIPMENT_PAGE_NEW)) {
                $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
            }
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

                    'description:ntext',
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],
                    'segment_path_id',
                    'equipment_id',
                    'value',

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