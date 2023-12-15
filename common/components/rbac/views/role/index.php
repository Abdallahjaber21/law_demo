<?php

use common\components\rbac\models\AuthItemSearch;
use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\models\Account;
use common\widgets\dashboard\PanelBox;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this View */
/* @var $searchModel AuthItemSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Roles');
$this->params['breadcrumbs'][] = Yii::t("app", 'Roles & Permissions');
$this->params['breadcrumbs'][] = $this->title;

$models = $dataProvider->getmodels();

$crud_opts = ArrayHelper::getColumn(Account::getAdminHierarchy(false), 'name');

foreach ($models as $index => $m) {
    if (!in_array($m->name, $crud_opts)) {
        unset($models[$index]);
    }
}

$new_data_provider = new ArrayDataProvider([
    'allModels' => $models,
])
?>

<div class="permission-index">

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php
            $panel = PanelBox::begin([
                'title' => $this->title,
                'icon'  => 'lock',
                'color' => PanelBox::COLOR_BLUE
            ]);
            ?>
            <!-- <//?php if (P::c(P::ADMINS_ROLE_PAGE_ADD_ROLE)) { ?>
                <//?php $panel->addButton(Yii::t('app', 'Add Role'), ['create'], ['class' => 'btn btn-primary btn-flat']) ?>
            <//?php } ?> -->
            <?=
            GridView::widget([
                'dataProvider' => $new_data_provider,
                'filterModel'  => $searchModel,
                'columns'      => [
                    [
                        'class'         => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    'name',
                    'description',
                    // [
                    //     'label' => $searchModel->attributeLabels()['ruleName'],
                    //     'value' => function ($model) {
                    //         return $model->ruleName == null ? Yii::t('rbac', '(not use)') : $model->ruleName;
                    //     }
                    // ],
                    [
                        'class'          => ActionColumn::className(),
                        'template'       => P::c(P::ADMINS_ROLE_PAGE_VIEW) ? '{update}' : '',
                        'headerOptions'  => ['style' => 'width:50px'],
                        'visibleButtons' => [
                            'update' => function ($model, $key, $index) {
                                if (P::c(P::ADMINS_ROLE_PAGE_VIEW)) {
                                    return true;
                                } else {
                                    if ($model->name == "developer") {
                                        return false;
                                    } else {
                                        return true;
                                    }
                                }
                            },
                        ]
                    ],
                ],
            ]);
            ?>
            <?php PanelBox::end() ?>
        </div>
    </div>
</div>