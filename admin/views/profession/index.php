<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\models\Profession;
use yii\grid\GridView;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use yii\helpers\ArrayHelper;
use common\models\Technician;
use common\models\users\Account;
use common\models\users\Admin;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProfessionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Professions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profession-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?> <?php if (P::c(P::CONFIGURATIONS_PROFESSION_PAGE_NEW)) {
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
                            $url = \yii\helpers\Url::to(['profession/view', 'id' => $model->id]);
                            return \yii\helpers\Html::a($model->id, $url);
                        },
                    ],

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
                    ],                    [
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
                        'value'     => function (Profession $model) {

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
                        'data'      =>
                        ArrayHelper::map(Admin::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
                        'format' => 'raw',
                        'visible' => P::c(P::CONFIGURATIONS_PROFESSION_PAGE_CATEGORIES),
                        'value' => function ($model) {
                            $url = ['profession-category/index',  'prof_id' => $model->id];

                            if (P::c(P::CONFIGURATIONS_PROFESSION_PAGE_CATEGORIES)) {
                                return Html::a(
                                    'Categories',
                                    $url,
                                    ['class' => 'btn btn-xs btn-primary']
                                );
                            }
                        },
                    ],


                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete} {audit}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                $parentClass = 'common\models\Profession';
                                $parentId = $model->id;
                                $childClass = 'common\models\Technician';
                                $paramsAttribute = 'profession_id';
                                $canMove = Yii::$app->user->can('management_deleted-entities_permission_move');
                                if ($model->status == Profession::STATUS_DELETED) {
                                    if (P::c(P::CONFIGURATIONS_PROFESSION_PAGE_DELETE)) {
                                        return Html::beginForm(['/dynamic/undelete', 'currentClass' => $parentClass, 'currentID' => $parentId, 'childClass' => $childClass, 'paramsAttribute' => $paramsAttribute], 'post') .
                                            Html::submitButton('Undelete', ['class' => 'btn btn-xs btn-warning', 'style' => ';margin-right:2px', 'data-confirm' => 'Are you sure you want to undelete this item?']) .
                                            Html::endForm();
                                    }
                                } else {
                                    if (P::c(P::CONFIGURATIONS_PROFESSION_PAGE_DELETE)) {
                                        $buttonOptions = [
                                            'class' => 'btn btn-xs btn-danger delete-button',
                                            'data-parent-class' => $parentClass,
                                            'data-parent-id' => $parentId,
                                            'data-child-class' => $childClass,
                                            'data-child-name' => 'Technician',
                                            'data-url-path' => Yii::$app->request->url,
                                            'data-parent-name' => 'Profession',
                                            'style' => 'min-width:57px;',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'data-params-attribute' => $paramsAttribute,
                                            'data-deleted-name' => $model->name,
                                        ];


                                        if ($canMove) {
                                            $buttonOptions['title'] = 'Make sure to delete or move all the children. Otherwise this main sector will not be deleted.';
                                        }
                                        return Html::a('Delete', 'javascript:void(0);', $buttonOptions);
                                    }
                                }
                            },
                        ],
                        'permissions' => [
                            'view' => P::CONFIGURATIONS_PROFESSION_PAGE_VIEW,
                            'update' => P::CONFIGURATIONS_PROFESSION_PAGE_UPDATE,
                            'enable' =>  P::CONFIGURATIONS_PROFESSION_PAGE_UPDATE,
                            'disable' =>  P::CONFIGURATIONS_PROFESSION_PAGE_UPDATE,
                            'delete' => P::CONFIGURATIONS_PROFESSION_PAGE_DELETE,
                            'audit' => P::CONFIGURATIONS_PROFESSION_PAGE_AUDIT,
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