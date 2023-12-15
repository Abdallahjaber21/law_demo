<?php

use common\components\extensions\ActionColumn;
use common\widgets\dashboard\PanelBox;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\Menu */

$this->title = Yii::t('app', 'Roles Assignments');
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
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    Yii::$app->getModule('rbac')->userModelIdField,
                    Yii::$app->getModule('rbac')->userModelLoginField,
                    [
                        'label' => 'Roles',
                        'content' => function($model) {
                            $authManager = Yii::$app->authManager;
                            $idField = Yii::$app->getModule('rbac')->userModelIdField;
                            $roles = [];
                            foreach ($authManager->getRolesByUser($model->{$idField}) as $role) {
                                $roles[] = $role->name;
                            }
                            if (count($roles) == 0) {
                                return Yii::t("yii", "(not set)");
                            } else {
                                return implode(",", $roles);
                            }
                        }
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'template' => Yii::$app->getUser()->can("rbac-edit-assignments") ? '{assignment}' : '',
                        'headerOptions' => ['style' => 'width:100px'],
                        'buttons' => [
                            'assignment' => function ($url, $model, $key) {
                                return Html::a('Assignments', $url, [
                                            'class' => 'btn btn-xs btn-primary btn-flat',
                                ]);
                            },
                        ],
                        'visibleButtons' => [
                            'assignment' => function ($model, $key, $index) {
                                if (Yii::$app->getUser()->can("developer")) {
                                    return true;
                                } else {
                                    $authManager = Yii::$app->authManager;
                                    $idField = Yii::$app->getModule('rbac')->userModelIdField;
                                    $roles = [];
                                    $isDeveloper = false;
                                    foreach ($authManager->getRolesByUser($model->{$idField}) as $role) {
                                        if ($role->name == "developer") {
                                            $isDeveloper = true;
                                        }
                                    }
                                    if ($isDeveloper) {
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