<?php

use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\CauseCode;
use common\models\DamageCode;
use common\models\Manufacturer;
use common\models\ObjectCategory;
use common\models\ObjectCode;
use common\widgets\dashboard\PanelBox;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LineItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Line Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-item-index">

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
                        'attribute' => 'object_code_id',
                        'value' => 'objectCode.name',
                        'data' => ArrayHelper::map(ObjectCode::find()->all(), 'id', 'name')
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'cause_code_id',
                        'value' => 'causeCode.name',
                        'data' => ArrayHelper::map(CauseCode::find()->all(), 'id', 'name')
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'damage_code_id',
                        'value' => 'damageCode.name',
                        'data' => ArrayHelper::map(DamageCode::find()->all(), 'id', 'name')
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'manufacturer_id',
                        'value' => 'manufacturer.name',
                        'data' => ArrayHelper::map(Manufacturer::find()->all(), 'id', 'name')
                    ],
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::className()
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::className()
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