<?php

use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\widgets\dashboard\PanelBox;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Articles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon'  => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?><?php if (P::c(P::MISC_MANAGE_NEWS)) {
                    $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
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
                        'attribute'     => 'image',
                        'value'         => function ($model) {
                            return Html::img($model->image_thumb_url, ['width' => '50']);
                        },
                        'format'        => 'html',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],

                    'title',
                    'subtitle',
                    //'content:ntext',
                    [
                        'attribute' => 'status',
                        'class'     => common\components\extensions\OptionsColumn::className()
                    ],
                    [
                        'attribute' => 'created_at',
                        'format'    => 'datetime',
                        'class'     => common\components\extensions\DateColumn::className()
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