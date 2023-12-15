<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Account;
use common\models\AccountType;
use common\models\Division;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Technician;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AccountTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Account Types';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="account-type-index">

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
            if (P::c(P::ADMINS_ACCOUNT_TYPE_PAGE_NEW)) {
                $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat']);
            }
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'summary' => false,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px'],
                        'value' => function ($model) {
                            return Html::a($model->id, Url::to(['view', 'id' => $model->id]));
                        },
                        'format' => 'raw'
                    ],
                    'label',
                    // 'name',
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'division_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            return @Division::findOne($model->division_id)->name;
                        },
                        'data' => ArrayHelper::map(Division::find()->all(), 'id', 'name'),
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'role_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            return $model->role_id;
                        },
                        'data' => ArrayHelper::map(AccountType::find()->orderBy(['name' => SORT_ASC])->all(), 'name', 'label'),
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'parent_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            return @$model->parent->label;
                        },
                        'data' => ArrayHelper::map(AccountType::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'label'),
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' =>  'for_backend',
                        'value' => function ($model) {
                            $color = ($model->for_backend == 1) ? '#28a745;' : '#dc3545';
                            return Html::tag('div',  ' ', ['style' => 'width:100%;height:20px;background-color:' . $color . '']);
                        },
                        'format' => 'raw',
                        'data' => [
                            '1' => 'True',
                            '0' => 'False',
                        ],
                    ],
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],

                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                $parentClass = 'common\models\AccountType';
                                $parentId = $model->id;
                                if ($model->for_backend == 1) {
                                    $childClass = 'common\models\Admin';
                                    $childname = 'Admin';
                                } else {
                                    $childClass = 'common\models\Technician';
                                    $childname = 'Technician';
                                }
                                // $childClass = 'common\models\Account';
                                // $childname = 'Account';
                                $paramsAttribute = 'type';
                                $canMove = Yii::$app->user->can('management_deleted-entities_permission_move');
                                if ($model->status == Technician::STATUS_DELETED) {
                                    if (P::c(P::ADMINS_ACCOUNT_TYPE_PAGE_DELETE)) {
                                        return Html::beginForm(['/dynamic/undelete', 'currentClass' => $parentClass, 'currentID' => $parentId, 'childClass' => $childClass, 'paramsAttribute' => $paramsAttribute], 'post') .
                                            Html::submitButton('Undelete', ['class' => 'btn btn-xs btn-warning', 'data-confirm' => 'Are you sure you want to undelete this item?']) .
                                            Html::endForm();
                                    }
                                } else {
                                    if (P::c(P::ADMINS_ACCOUNT_TYPE_PAGE_DELETE)) {
                                        $buttonOptions = [
                                            'class' => 'btn btn-xs btn-danger delete-button',
                                            'data-parent-class' => $parentClass,
                                            'data-parent-id' => $parentId,
                                            'data-child-class' => $childClass,
                                            'data-child-name' => $childname,
                                            'data-url-path' => Yii::$app->request->url,
                                            'data-parent-name' => 'Account Type',
                                            'style' => 'min-width:57px;',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'data-params-attribute' => $paramsAttribute,
                                            'data-deleted-name' => $model->name,
                                        ];

                                        if ($canMove) {
                                            $buttonOptions['title'] = 'Make sure to delete or move all the users or technicians. Otherwise this account type will not be deleted.';
                                        }
                                        return Html::a('Delete', 'javascript:void(0);', $buttonOptions);
                                    }
                                }
                            },
                        ],
                        'permissions' => [
                            'view' => P::ADMINS_ACCOUNT_TYPE_PAGE_VIEW,
                            'update' => P::ADMINS_ACCOUNT_TYPE_PAGE_UPDATE,
                            'delete' =>  P::ADMINS_ACCOUNT_TYPE_PAGE_DELETE,
                        ],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
<script>
    var updateParentStatusUrl = "<?= Url::to(['/dynamic/update-parent-status']) ?>";
</script>
<style>
    .grid-view tr td:last-child {
        display: flex;
        justify-content: space-between !important;
    }

    .grid-view tr td:last-child a {
        margin-right: 2px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js">
</script>
<?php $this->registerJsFile("@staticWeb/js/dynamic.js"); ?>