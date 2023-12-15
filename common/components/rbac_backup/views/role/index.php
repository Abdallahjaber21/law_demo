<?php

use common\components\rbac\models\AuthItemSearch;
use common\components\extensions\ActionColumn;
use common\widgets\dashboard\PanelBox;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this View */
/* @var $searchModel AuthItemSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Roles');
$this->params['breadcrumbs'][] = Yii::t("app", 'Roles & Permissions');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="permission-index">

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php
            $panel = PanelBox::begin([
                        'title' => $this->title,
                        'icon' => 'lock',
                        'color' => PanelBox::COLOR_BLUE
            ]);
            ?>
            <?php if (Yii::$app->getUser()->can("rbac-create-roles")) { ?>
                <?php $panel->addButton(Yii::t('app', 'Add Role'), ['create'], ['class' => 'btn btn-primary btn-flat']) ?>
            <?php } ?>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    'name',
                    'description',
                    [
                        'label' => $searchModel->attributeLabels()['ruleName'],
                        'value' => function($model) {
                            return $model->ruleName == null ? Yii::t('rbac', '(not use)') : $model->ruleName;
                        }
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'template' => Yii::$app->getUser()->can("rbac-edit-roles") ? '{update}' : '',
                        'headerOptions' => ['style' => 'width:50px'],
                        'visibleButtons' => [
                            'update' => function ($model, $key, $index) {
                                if (Yii::$app->getUser()->can("developer")) {
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
