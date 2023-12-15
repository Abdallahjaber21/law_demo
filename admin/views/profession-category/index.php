<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\models\Category;
use common\models\Profession;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProfessionCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Profession Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profession-category-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_ORANGE
            ]);
            ?> <?php if (Yii::$app->getUser()->can("developer")) {
                    // $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
                }
                ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [

                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                    ],

                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'profession_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->profession->name)) {
                                return $model->profession->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Profession::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'category_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->category->name)) {
                                return $model->category->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Category::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],


                    // [
                    //     'attribute' => 'status',
                    //     'class' => common\components\extensions\OptionsColumn::class
                    // ],



                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>