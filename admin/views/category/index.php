<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\OptionsColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Category;
use common\models\Professions;
use common\models\Technician;
use common\models\Account;
use common\models\users\Admin;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\widgets\inputs\assets\ICheckAsset;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
$pageId = Yii::$app->controller->id;
$modelname = Category::className();
$attributes = Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
$attributeLabels = $attributes['attributeLabels'];
?>
<div class="category-index">

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
            <div class="button-container <?php echo (P::c(P::CONFIGURATIONS_CATEGORY_PAGE_NEW)) ? '' : 'buttonpostion'; ?>">
                <?php if (P::c(P::CONFIGURATIONS_CATEGORY_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat', 'style' => 'margin-right:10px']);
                } ?> <?php $panel->addButton(Yii::t('app', FA::i(FA::_EDIT) . ' Edit'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'style' => 'margin-right:10px', 'id' => 'show-buttons']);
                        $panel->addButton(Yii::t('app', 'Cancel'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'style' => 'margin-right:10px;display:none', 'id' => 'cancel_btn']);
                        ?>

                <?php if ((P::c(P::CONFIGURATIONS_CATEGORY_PAGE_EXPORT))) { ?>

                    <div class="form-group ">
                        <?= Html::beginForm(['category/index'] + Yii::$app->request->queryParams, 'GET', ['class' => 'form-inline', 'id' => 'date-range-form']) ?>
                        <?= Html::submitButton(
                            Yii::t('app', 'Export PDF'),
                            [
                                'form' => 'date-range-form',
                                'class' => 'btn btn-danger btn-flat btn-sm',
                                'name' => 'export',
                                'value' => 'pdf',
                                'target' => '_blank',
                            ]
                        ); ?>
                        <?= Html::endForm() ?>
                    </div><?php } ?>
                <?= $this->render('@app/views/common/_hidden', [
                    'hiddenAttributes' => $hiddenAttributes,
                    'model' => $searchModel,
                ]) ?>
            </div>


            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [

                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],
                        'visible' => !in_array('id', $hiddenAttributes),
                        'format' => 'raw',
                        'value' => function ($model) {
                            $url = Url::to(['category/view', 'id' => $model->id]);
                            return Html::a($model->id, $url);
                        },
                    ],

                    [
                        'attribute' => 'name',
                        'visible' => !in_array('name', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'name']
                    ],
                    [
                        'attribute' => 'code',
                        'visible' => !in_array('code', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header',  'data-attribute' => 'code']
                    ],
                    [
                        'attribute' => 'status',
                        'visible' => !in_array('status', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'status'],
                        'class' => OptionsColumn::class
                    ],
                    [
                        'attribute' => 'description',
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'description'],
                        'visible' => !in_array('description', $hiddenAttributes),
                        'value' => function ($model) {
                            return @$model->description;
                        },
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'parent_id',
                        'visible' => !in_array('parent_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'parent_id'],
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
                        'attribute'      => 'created_at',
                        'format'         => 'datetime',
                        'visible' => !in_array('created_at', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'created_at'],
                        'class'          => common\components\extensions\DateRangeColumn::className(),
                        'attribute_from' => 'created_at_from',
                        'attribute_to'   => 'created_at_to',
                        'label'          => 'Created At',

                    ],

                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'created_by',
                        'label'     => 'Created By',
                        'visible' => !in_array('created_by', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'created_by'],
                        'value'     => function (Category $model) {
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
                        'visible' => !in_array('updated_at', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'updated_at'],
                        'class'          => common\components\extensions\DateRangeColumn::className(),
                        'attribute_from' => 'updated_at_from',
                        'attribute_to'   => 'updated_at_to',
                        'label'          => 'Updated At',

                    ],
                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'updated_by',
                        'visible' => !in_array('updated_by', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'updated_by'],
                        'label'     => 'Updated By',
                        'value'     => function (Category $model) {
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
                        'visible' => P::c(P::CONFIGURATIONS_CATEGORY_PAGE_PROFESSIONS),
                        'value' => function ($model) {
                            $url = ['profession-category/index',  'cat_id' => $model->id];
                            return Html::a(
                                'Professions',
                                $url,
                                ['class' => 'btn btn-xs btn-primary']
                            );
                        },
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete} {audit}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                $parentClass = 'common\models\Category';
                                $parentId = $model->id;
                                $childClass = 'common\models\EquipmentType';
                                $paramsAttribute = 'category_id';
                                $canMove = Yii::$app->user->can('management_deleted-entities_permission_move');
                                if ($model->status == Category::STATUS_DELETED) {
                                    if (P::c(P::CONFIGURATIONS_CATEGORY_PAGE_UPDATE)) {
                                        return Html::beginForm(['/dynamic/undelete', 'parentClass' => $parentClass, 'parentID' => $parentId, 'childClass' => $childClass, 'paramsAttribute' => $paramsAttribute], 'post') .
                                            Html::submitButton('Undelete', ['class' => 'btn btn-xs btn-warning', 'style' => 'margin-right:2px;', 'data-confirm' => 'Are you sure you want to undelete this item?']) .
                                            Html::endForm();
                                    }
                                } else {
                                    if (P::c(P::CONFIGURATIONS_CATEGORY_PAGE_UPDATE)) {
                                        $buttonOptions = [
                                            'class' => 'btn btn-xs btn-danger delete-button',
                                            'data-parent-class' => $parentClass,
                                            'data-parent-id' => $parentId,
                                            'data-child-class' => $childClass,
                                            'data-child-name' => 'Equipment Type',
                                            'data-url-path' => Yii::$app->request->url,
                                            'data-parent-name' => 'Category',
                                            'style' => 'min-width:57px;',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'data-params-attribute' => $paramsAttribute,
                                            'data-deleted-name' => $model->name,
                                        ];


                                        if ($canMove) {
                                            $buttonOptions['title'] = 'Make sure to delete or move all the category parent. Otherwise this catgory will not be deleted.';
                                        }
                                        return Html::a('Delete', 'javascript:void(0);', $buttonOptions);
                                    }
                                }
                            },
                        ],
                        'permissions' => [
                            'view' => P::CONFIGURATIONS_CATEGORY_PAGE_VIEW,
                            'update' => P::CONFIGURATIONS_CATEGORY_PAGE_UPDATE,
                            'enable' =>  P::CONFIGURATIONS_CATEGORY_PAGE_UPDATE,
                            'disable' =>  P::CONFIGURATIONS_CATEGORY_PAGE_UPDATE,
                            'delete' =>  P::CONFIGURATIONS_CATEGORY_PAGE_DELETE,
                            'audit' =>  P::CONFIGURATIONS_CATEGORY_PAGE_AUDIT,
                        ],
                    ],
                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>
<?php ICheckAsset::register($this) ?>
<script>
    <?php ob_start(); ?>
    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-green',
        increaseArea: '20%',

    });
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>

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