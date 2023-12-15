<?php

use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Customer;
use common\models\ObjectCategory;
use common\widgets\dashboard\PanelBox;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ObjectCodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Object Codes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-code-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?><?php if (\common\config\includes\P::c(\common\config\includes\P::MISC_MANAGE_LINE_ITEMS)) {
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

                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'object_category_id',
                        'value' => 'objectCategory.name',
                        'data' => ArrayHelper::map(ObjectCategory::find()->all(), 'id', 'name')
                    ],
                    'code',
                    'name',
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
                        'template' => '{view} {update} ',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>