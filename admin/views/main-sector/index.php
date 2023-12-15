<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\models\MainSector;
use yii\grid\GridView;
use common\models\Division;
use common\models\User;
use common\models\users\Account;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use yii\helpers\ArrayHelper;
use common\models\users\Admin;
use common\models\Sector;
use yii\widgets\Pjax;
use rmrevin\yii\fontawesome\FA;
use common\widgets\inputs\assets\ICheckAsset;
use yii\helpers\Url;


$this->title = 'Main Sectors';
$this->params['breadcrumbs'][] = $this->title;

$pageId = Yii::$app->controller->id;
$modelname = MainSector::className();
$attributes = common\models\Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
$attributeLabels = $attributes['attributeLabels'];
?>

<div class="main-sector-index">

    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]); ?>
            <div class="button-container <?php echo (P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_NEW)) ? '' : 'buttonpostion'; ?>">

                <?php if (P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat', 'style' => 'margin-right:10px']);
                }
                $panel->addButton(Yii::t('app', FA::i(FA::_EDIT) . ' Edit'), 'javascript:void(0);', ['class' => 'btn
                btn-warning', 'style' => 'margin-right:10px', 'id' => 'show-buttons']);
                $panel->addButton(Yii::t('app', 'Cancel'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'style'
                => 'margin-right:10px;display:none', 'id' => 'cancel_btn']);
                ?>
                <?= $this->render('@app/views/common/_hidden', [
                    'hiddenAttributes' => $hiddenAttributes,
                    'model' => $searchModel,
                ]) ?> </div>
            <?php Pjax::begin(['id' => 'grid-view-container']); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [

                    [
                        'attribute' => 'id',
                        'visible' => !in_array('id', $hiddenAttributes),
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],                        'format' => 'raw',
                        'value' => function ($model) {
                            $url = \yii\helpers\Url::to(['main-sector/view', 'id' => $model->id]);
                            return \yii\helpers\Html::a($model->id, $url);
                        },
                    ],

                    [
                        'attribute' =>    'name',
                        'visible' => !in_array('name', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'name'],
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'division_id',
                        'visible' => !in_array('division_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'division_id'],
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->division->name)) {
                                $link = ['division/view', 'id' => $model->division_id];
                                return $model->division->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
                        'attribute' => 'description',
                        'visible' => !in_array('description', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'description'],
                        'value' => function ($model) {

                            return $model->description;
                        },
                    ],                    [
                        'attribute' => 'status',
                        'visible' => !in_array('status', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'status'],
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute'      => 'created_at',
                        'visible' => !in_array('created_at', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'created_at'],
                        'format'         => 'datetime',
                        'class'          => common\components\extensions\DateRangeColumn::className(),
                        'attribute_from' => 'created_at_from',
                        'attribute_to'   => 'created_at_to',
                        'label'          => 'Created At',

                    ],
                    [
                        'class'     => RelationColumn::className(),
                        'attribute' => 'created_by',
                        'visible' => !in_array('created_by', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'created_by'],
                        'label'     => 'Created By',
                        'value'     => function (MainSector $model) {

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
                        'visible' => !in_array('updated_by', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'updated_by'],
                        'attribute' => 'updated_by',
                        'label'     => 'Updated By',
                        'value'     => function (MainSector $model) {
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
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete} {audit}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                $parentClass = 'common\models\MainSector';
                                $parentId = $model->id;
                                $childClass = 'common\models\Sector';
                                $paramsAttribute = 'main_sector_id';
                                $canMove = Yii::$app->user->can('management_deleted-entities_permission_move');
                                if ($model->status == Sector::STATUS_DELETED) {
                                    if (P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_DELETE)) {

                                        return Html::beginForm(['/dynamic/undelete', 'currentClass' => $parentClass, 'currentId' => $parentId, 'childClass' => $childClass, 'paramsAttribute' => $paramsAttribute], 'post') .
                                            Html::submitButton('Undelete', ['class' => 'btn btn-xs btn-warning', 'style' => ';margin-right:2px', 'data-confirm' => 'Are you sure you want to undelete this item?']) .
                                            Html::endForm();
                                    }
                                } else {
                                    if (P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_DELETE)) {
                                        $buttonOptions = [
                                            'class' => 'btn btn-xs btn-danger delete-button',
                                            'data-parent-class' => $parentClass,
                                            'data-parent-id' => $parentId,
                                            'data-child-class' => $childClass,
                                            'data-child-name' => 'Sector',
                                            'data-url-path' => Yii::$app->request->url,
                                            'data-parent-name' => 'Main Sector',
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
                            'view' => P::CONFIGURATIONS_MAIN_SECTOR_PAGE_VIEW,
                            'update' => P::CONFIGURATIONS_MAIN_SECTOR_PAGE_UPDATE,
                            'enable' => P::CONFIGURATIONS_MAIN_SECTOR_PAGE_UPDATE,
                            'disable' => P::CONFIGURATIONS_MAIN_SECTOR_PAGE_UPDATE,
                            'delete' => P::CONFIGURATIONS_MAIN_SECTOR_PAGE_DELETE,
                            'audit' => P::CONFIGURATIONS_MAIN_SECTOR_PAGE_AUDIT,
                        ],
                    ],
                ],
            ]);
            ?>
            <?php Pjax::end(); ?>

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
<?php ICheckAsset::register($this) ?>
<script>
    <?php ob_start(); ?>
    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-green',
        increaseArea: '20%'
    });
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>