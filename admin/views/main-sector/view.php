<?php

use common\config\includes\P;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use common\models\MainSector;
use yii\helpers\ArrayHelper;
use common\models\Technician;
use common\models\User;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use common\models\users\Account;
use common\models\users\Admin;
/* @var $this yii\web\View */
/* @var $model common\models\MainSector */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Main Sectors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$pageId = Yii::$app->controller->id;
$modelname = MainSector::className();
$attributes = common\models\Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
?>
<div class="main-sector-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_UPDATE)) { ?>
            <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'visible'   => !in_array('id', $hiddenAttributes),

                    ],
                    [
                        'attribute' => 'name',
                        'visible'   => !in_array('name', $hiddenAttributes),

                    ],
                    [
                        'attribute' => 'division_id',
                        'value'     => Html::tag("p", $model->division->name),
                        'format'    => 'html',
                        'visible'   => !in_array('division_id', $hiddenAttributes),

                    ],
                    [
                        'attribute' => 'description',
                        'visible'   => !in_array('description', $hiddenAttributes),
                        'value' => function ($model) {

                            return $model->description;
                        },
                    ],                              [
                        'attribute' => 'status',
                        'value' => $model->status_label,
                        'visible'   => !in_array('status', $hiddenAttributes),


                    ],                    [
                        'attribute' => 'created_at',
                        'class' => common\components\extensions\DateColumn::class,
                        'format' => 'datetime',
                        'visible'   => !in_array('created_at', $hiddenAttributes),


                    ],
                    [
                        'attribute' => 'updated_at',
                        'visible'   => !in_array('updated_at', $hiddenAttributes),
                        'class' => common\components\extensions\DateColumn::class,
                        'format' => 'datetime',

                    ],
                    [
                        'attribute' => 'created_by',
                        'visible'   => !in_array('created_by', $hiddenAttributes),
                        'value'     => function (MainSector $model) {

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
                        'data' => ArrayHelper::map(Admin::find()->all(), 'id', 'name')

                    ],
                    [
                        'attribute' => 'updated_by',
                        'visible'   => !in_array('updated_by', $hiddenAttributes),
                        'value'     => function (MainSector $model) {

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
                        'data' => ArrayHelper::map(Admin::find()->all(), 'id', 'name')

                    ],
                ],
            ]) ?>

            <?php PanelBox::end() ?> </div>
        <?php
        $dataprovider = new ArrayDataProvider(
            [
                'allModels' => $model->sectors,
            ]
        ); ?>
        <?php if (P::c(P::CONFIGURATIONS_SECTOR_PAGE_VIEW)) : ?>

        <div class="col-md-12">
            <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode('Sectors'),
                    'icon' => 'eye',
                    'color' => PanelBox::COLOR_GREEN
                ]);
                ?>
            <?php if (P::c(P::CONFIGURATIONS_SECTOR_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['sector/create', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']);
                }
                ?>
            <?= GridView::widget([
                    'dataProvider' => $dataprovider,
                    'filterModel' => null,
                    'columns' => [
                        [
                            'attribute' => 'name',
                            'value' => function ($model) {
                                return $model->name;
                            }
                        ],
                        'code',
                        'description',

                        [
                            'attribute' => 'status',
                            'class' => common\components\extensions\OptionsColumn::class
                        ],
                        [
                            'attribute' => 'created_at',
                            'class' => common\components\extensions\DateColumn::class
                        ],


                    ],
                ]); ?>

            <?php PanelBox::end() ?>
        </div>
        <?php endif; ?>
    </div>
</div>