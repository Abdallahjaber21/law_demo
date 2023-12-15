<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BlockedIp */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Blocked Ips';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blocked-ip-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <div class="row">
        <div class="col-md-12">
            <?php            $panel = PanelBox::begin([
                        'title' => $this->title,
                        'icon' => 'table',
                        'color' => PanelBox::COLOR_GRAY
            ]);
           ?>            <?php            if (Yii::$app->getUser()->can("developer")) {
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

            'ip_address',
[
    'attribute' => 'status',
    'class' => common\components\extensions\OptionsColumn::class
],
[
    'attribute' => 'created_at',
    'format' => 'datetime',
    'class'=> common\components\extensions\DateColumn::class
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
