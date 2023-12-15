<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\models\State;
use yii\grid\GridView;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use yii\helpers\ArrayHelper;
use common\models\Technician;
use common\models\users\Account;
use common\models\users\Admin;
use common\models\City;


/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?> <?php if (P::c(P::CONFIGURATIONS_CITY_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
                }
                ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [

                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            $url = \yii\helpers\Url::to(['city/view', 'id' => $model->id]);
                            return \yii\helpers\Html::a($model->id, $url);
                        },
                    ],


                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'state_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty(@$model->state->name)) {
                                $link = ['state/view', 'id' => $model->state_id];
                                return Html::a(@$model->state->name, $link);
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(State::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],


                    'name',
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
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
                        'value'     => function (City $model) {

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
                        'value'     => function (City $model) {
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

                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'permissions' => [
                            'view' => P::CONFIGURATIONS_CITY_PAGE_VIEW,
                            'update' => P::CONFIGURATIONS_CITY_PAGE_UPDATE,
                            'enable' =>  P::CONFIGURATIONS_CITY_PAGE_UPDATE,
                            'disable' =>  P::CONFIGURATIONS_CITY_PAGE_UPDATE,
                            'delete' => false
                        ],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>