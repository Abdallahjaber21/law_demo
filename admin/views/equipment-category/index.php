<?php

use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\widgets\dashboard\PanelBox;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EquipmentCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equipment Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-category-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon'  => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?><?php if (Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin")) {
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

                    'key',
                    'name',

                    [
                        'class'         => ActionColumn::className(),
                        'template'      => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>

<?php //echo implode("<br/>",(new \common\models\Equipment())->unit_type_list) 
?>