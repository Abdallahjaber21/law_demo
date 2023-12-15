<?php

use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\models\Account;
use common\widgets\dashboard\PanelBox;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\Menu */

$this->title = Yii::t('app', 'Roles Assignments');
$this->params['breadcrumbs'][] = Yii::t("app", 'Roles & Permissions');
$this->params['breadcrumbs'][] = $this->title;

$models = $dataProvider->getModels();
$out = [];

// $crud_opts = Account::getAdminOptionsCrud();
$crud_opts = ArrayHelper::getColumn(Account::getAdminHierarchy(false), 'name');

foreach ($models as $index => $m) {
    if (in_array($m->account->type0->name, $crud_opts)) {
        $out[$m->account_id] = $m;
    }
}

$newDataProvider = new ArrayDataProvider([
    'allModels' => $out
]);
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
            <?=
            GridView::widget([
                'dataProvider' => $newDataProvider,
                //'filterModel' => $searchModel,
                'columns'      => [
                    [
                        'class'         => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                    Yii::$app->getModule('rbac')->userModelIdField,
                    Yii::$app->getModule('rbac')->userModelLoginField,
                    [
                        'label'   => 'Roles',
                        'content' => function ($model) {
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
                        'class'          => ActionColumn::className(),
                        'template'       => P::c(P::ADMINS_ROLE_PAGE_VIEW) ? '{assignment}' : '',
                        'headerOptions'  => ['style' => 'width:100px'],
                        'buttons'        => [
                            'assignment' => function ($url, $model, $key) {
                                return Html::a('Assignments', $url, [
                                    'class' => 'btn btn-xs btn-primary btn-flat',
                                ]);
                            },
                        ],
                        'visibleButtons' => [
                            'assignment' => function ($model, $key, $index) {
                                if ($model->account_id != Yii::$app->user->id) {
                                    if (P::c(P::ADMINS_ROLE_PAGE_VIEW)) {
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