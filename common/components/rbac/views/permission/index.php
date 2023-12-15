<?php

use common\components\rbac\models\AuthItemSearch;
use common\components\extensions\ActionColumn;
use common\config\includes\P;
use common\widgets\dashboard\PanelBox;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $searchModel AuthItemSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Permissions');
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
            <?php if (P::c(P::ADMINS_ROLE_PAGE_VIEW)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Add Permission'), ['create'], ['class' => 'btn btn-primary btn-flat']) ?>
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
                    // [
                    //     'label' => $searchModel->attributeLabels()['ruleName'],
                    //     'value' => function($model) {
                    //         return $model->ruleName == null ? Yii::t('rbac', '(not use)') : $model->ruleName;
                    //     }
                    // ],
                    [
                        'class' => ActionColumn::className(),
                        'template' => P::c(P::ADMINS_ROLE_PAGE_VIEW) ? '{update}' : '',
                        'headerOptions' => ['style' => 'width:50px'],
                    ],
                ],
            ]);
            ?>
            <?php PanelBox::end() ?>
        </div>
    </div>
</div>