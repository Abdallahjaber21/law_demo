<?php

use common\config\includes\P;
use common\models\Profession;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Technician;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use common\models\User;
use common\models\users\Account;
use common\components\extensions\RelationColumn;
use common\models\Category;
use common\models\users\Admin;

/* @var $this yii\web\View */
/* @var $model common\models\Profession */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Professions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="profession-view">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::CONFIGURATIONS_PROFESSION_PAGE_UPDATE)) { ?>
            <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>

            <?php
            $add_updated_by = Yii::$app->cache->get("cashing_user_name"); //to retreive the cash values
            if (empty($add_updated_by)) { //check if the cash is empty e have 3 different table related to user
                $adminUserCache = ArrayHelper::map(Admin::find()->select(['id', 'name'])->asArray()->all(), "id", "name");
                $technicianUserCache = ArrayHelper::map(Technician::find()->select(['id', 'name'])->asArray()->all(), "id", "name");
                $accountsCache = $adminUserCache + $technicianUserCache;
                Yii::$app->cache->set("accounts-cache", $accountsCache, 60 * 30);
            } ?> <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
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
                            ],                            [
                                'attribute' => 'status',
                                'value' => $model->status_label
                            ],
                            [
                                'attribute' => 'created_at',
                                'class' => common\components\extensions\DateColumn::class,
                                'format' => 'datetime',

                            ],
                            [
                                'attribute' => 'updated_at',
                                'class' => common\components\extensions\DateColumn::class,
                                'format' => 'datetime',

                            ],
                            [
                                'attribute' => 'created_by',
                                'value'     => function (Profession $model) {

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
                                'value'     => function (Profession $model) {
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
                'allModels' => $model->technicians,
            ]
        ); ?>
        <?php if (P::c(P::MANAGEMENT_TECHNICIAN_PAGE_VIEW)) : ?>

        <div class="col-md-12">
            <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode('Technician'),
                    'icon' => 'eye',
                    'color' => PanelBox::COLOR_GREEN
                ]);
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
                        [
                            'attribute' => 'account_type',
                            'value'     => function ($model) {
                                return @$model->account->type0->label;
                            },
                            'format'    => 'html'
                        ],
                        'phone_number',

                        [
                            'attribute' => 'status',
                            'class' => common\components\extensions\OptionsColumn::class
                        ],



                    ],
                ]); ?>

            <?php PanelBox::end() ?>
        </div>
        <?php endif; ?>
        <?php
        $categoryDataProvider = new ArrayDataProvider(
            [
                'allModels' => $model->categories,
            ]
        ); ?>
        <?php if (P::C(P::CONFIGURATIONS_CATEGORY_PAGE_VIEW) && (P::C(P::CONFIGURATIONS_PROFESSION_PAGE_CATEGORIES))) : ?>

        <div class="col-md-12">
            <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode('Categories'),
                    'icon' => 'eye',
                    'color' => PanelBox::COLOR_GREEN
                ]);
                ?>
            <?php if (P::c(P::CONFIGURATIONS_CATEGORY_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['category/create', 'profession_id' => $model->id], ['class' => 'btn btn-primary btn-flat']);
                }
                ?>

            <?= GridView::widget([
                    'dataProvider' => $categoryDataProvider,
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
                            'class' => RelationColumn::className(),
                            'attribute' => 'parent_id',
                            'format' => 'html',
                            'value' => function ($model) {
                                if (!empty($model->parent)) {
                                    $link = ['view', 'id' => $model->parent->id];
                                    return Html::a($model->parent->name, $link);
                                }
                                return null;
                            },
                            'data' => ArrayHelper::map(Category::find()->where(['parent_id' => null])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                            'filter' => ArrayHelper::map(Category::find()->where(['parent_id' => null])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),

                        ],
                        [
                            'attribute' => 'status',
                            'class' => common\components\extensions\OptionsColumn::class
                        ],



                    ],
                ]); ?>

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