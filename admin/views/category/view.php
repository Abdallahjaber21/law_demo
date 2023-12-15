<?php

use common\components\extensions\DateColumn;
use common\config\includes\P;
use common\models\Account;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use yii\helpers\ArrayHelper;
use common\models\Category;
use common\models\Technician;
use common\models\User;
use common\models\users\Admin;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use common\models\EquipmentType;

/* @var $this yii\web\View */
/* @var $model common\models\Category */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$add_updated_by = Yii::$app->cache->get("cashing_user_name"); //to retreive the cash values
$pageId = Yii::$app->controller->id;
$modelname = Category::className();
$attributes = Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
?>
<div class="category-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::CONFIGURATIONS_CATEGORY_PAGE_UPDATE)) { ?>
            <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?php $parentname = "";
            if (!empty($model->parent->name)) {
                $parentname = $model->parent->name;
            } ?>
            <?php
            if (empty($add_updated_by)) { //check if the cash is empty e have 3 different table related to user
                $adminUserCache = ArrayHelper::map(Admin::find()->select(['id', 'name'])->asArray()->all(), "id", "name");
                $technicianUserCache = ArrayHelper::map(Technician::find()->select(['id', 'name'])->asArray()->all(), "id", "name");
                $accountsCache = $adminUserCache + $technicianUserCache; //merg into one array
                Yii::$app->cache->set("accounts-cache", $accountsCache, 60 * 30);
            } ?>
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
                        'attribute' => 'code',
                        'visible'   => !in_array('Code', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'parent_id',
                        'visible'   => !in_array('parent_id', $hiddenAttributes),
                        'value'     => Html::tag("p", $parentname),
                        'format'    => 'html'
                    ],
                    [
                        'attribute' => 'description',
                        'visible'   => !in_array('description', $hiddenAttributes),
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
                        'visible'   => !in_array('status', $hiddenAttributes),
                        'value' => $model->status_label
                    ],                     [
                        'attribute' => 'created_at',
                        'visible'   => !in_array('created_at', $hiddenAttributes),
                        'class' => DateColumn::class,
                        'format' => 'datetime',

                    ],
                    [
                        'attribute' => 'updated_at',
                        'visible'   => !in_array('updated_at', $hiddenAttributes),
                        'class' => DateColumn::class,
                        'format' => 'datetime',

                    ],
                    [
                        'attribute' => 'created_by',
                        'visible'   => !in_array('created_by', $hiddenAttributes),
                        'value'     => function (Category $model) {

                            if (!empty($model->created_by)) {
                                $account = Account::findOne($model->created_by);
                                if (!empty($account)) {
                                    $admin = Admin::find()->where(['account_id' => $model->created_by])->one();
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
                        'value'     => function (Category $model) {

                            if (!empty($model->updated_by)) {
                                $account = Account::findOne($model->updated_by);
                                if (!empty($account)) {
                                    $admin = Admin::find()->where(['account_id' => $model->updated_by])->one();
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
                'allModels' => $model->equipmentTypes,
            ]
        ); ?>
        <?php if (P::C(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_VIEW)) : ?>
        <div class="col-md-12">
            <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode('Equipment Type'),
                    'icon' => 'eye',
                    'color' => PanelBox::COLOR_GREEN
                ]);
                ?>
            <?php if (P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['equipment-type/create', 'category_id' => $model->id], ['class' => 'btn btn-primary btn-flat']);
                }
                ?>
            <?php if (!empty($model->equipmentTypes)) : ?>
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
                            [
                                'attribute' => 'status',
                                'class' => common\components\extensions\OptionsColumn::class
                            ],



                        ],
                    ]); ?>
            <?php else : ?>
            <h3 class="no-data">No Data</h3>
            <?php endif; ?>
            <?php PanelBox::end() ?>
        </div>
        <?php endif; ?>
        <?php
        $professionDataProvider = new ArrayDataProvider(
            [
                'allModels' => $model->professions,
            ]
        ); ?>
        <?php if (P::C(P::CONFIGURATIONS_PROFESSION_PAGE_VIEW) && ((P::c(P::CONFIGURATIONS_CATEGORY_PAGE_PROFESSIONS)))) : ?>
        <div class="col-md-12">
            <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode('Professions'),
                    'icon' => 'eye',
                    'color' => PanelBox::COLOR_GREEN
                ]);
                ?>
            <?php if (P::c(P::CONFIGURATIONS_PROFESSION_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['profession/create', 'category_id' => $model->id], ['class' => 'btn btn-primary btn-flat']);
                }
                ?>
            <?php if (!empty($model->professions)) : ?>
            <?= GridView::widget([
                        'dataProvider' => $professionDataProvider,
                        'filterModel' => null,
                        'columns' => [
                            [
                                'attribute' => 'name',
                                'value' => function ($model) {
                                    return $model->name;
                                }
                            ],
                            'description',
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


                        ],
                    ]); ?>
            <?php else : ?>
            <h3 class="no-data">No Data</h3>
            <?php endif; ?>
            <?php PanelBox::end() ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<style>
.grid-view tr td:last-child {
    text-align: left
}
</style>