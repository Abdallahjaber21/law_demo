<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\extensions\RelationColumn;
use common\models\Division;
use yii\helpers\ArrayHelper;
use common\models\Technician;
use common\models\User;
use common\models\users\Account;
use common\models\Admin;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DivisionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Divisions';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="division-index">

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
            <?php
            $accountsCache = Yii::$app->cache->get("accounts-cache");

            if (empty($accountsCache)) {

                $adminCache = ArrayHelper::map(Admin::find()->select(['id', 'name'])->asArray()->all(), "id", "name");
                $accountsCache = $adminCache;
                Yii::$app->cache->set("accounts-cache", $accountsCache, 60 * 15);
            }

            ?>
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->id;
                        },
                    ],

                    'name',
                    [
                        'attribute' => 'description',
                        'value' => function ($model) {
                            if (empty($model->description)) {
                                return '-';
                            } else {
                                return $model->description;
                            }
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' =>  'has_shifts',
                        'value' => function ($model) {
                            $color = ($model->has_shifts == 1) ? '#28a745;' : '#dc3545';
                            return Html::tag('div',  ' ', ['style' => 'width:100%;height:20px;background-color:' . $color . '']);
                        },
                        'format' => 'raw',
                        'filter'    => Select2::widget([
                            'model'         => $searchModel,
                            'attribute'     => 'has_shifts',
                            'data'          => [
                                1 => 'Yes',
                                0 => 'No'
                            ],
                            'pluginOptions' => [
                                'multiple'   => false,
                                'allowClear' => true
                            ],
                            'options'       => [
                                'placeholder' => 'Has Shifts'
                            ],
                        ])
                    ],
                    [
                        'attribute'      => 'created_at',
                        'format'         => 'datetime',
                        'class'          => common\components\extensions\DateRangeColumn::className(),
                        'attribute_from' => 'created_at_from',
                        'attribute_to'   => 'created_at_to',
                        'label'          => 'Created At',
                    ],
                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'created_by',
                        'label'     => 'Created By',
                        'value'     => function (Division $model) {
                            if (!empty($model->created_by)) {
                                $account = Account::findOne($model->created_by);

                                if (!empty($account)) {
                                    $admin = Admin::find()->where(['id' => $model->created_by])->one();
                                    if (!empty($admin)) {

                                        return ($admin->name);
                                    }
                                }
                            }
                        },
                        'data'      =>
                        ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],


                    [
                        'attribute'      => 'updated_at',
                        'format'         => 'datetime',
                        'class'          => common\components\extensions\DateRangeColumn::className(),
                        'attribute_from' => 'updated_at_from',
                        'attribute_to'   => 'updated_at_to',
                        'label'          => 'Updated At',
                    ],
                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'updated_by',
                        'label'     => 'Updated By',
                        'value'     => function (Division $model) {
                            if (!empty($model->updated_by)) {
                                $account = Account::findOne($model->updated_by);

                                if (!empty($account)) {
                                    $admin = Admin::find()->where(['id' => $model->updated_by])->one();
                                    if (!empty($admin)) {

                                        return ($admin->name);
                                    }
                                }
                            }
                        },
                        'data'      =>
                        ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],


                ],
            ]); ?>
            <?php Pjax::end(); ?>
            <?php PanelBox::end() ?>
        </div>
    </div>
</div>