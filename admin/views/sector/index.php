<?php

use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\MainSector;
use common\widgets\dashboard\PanelBox;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\models\Sector;
use common\models\Technician;
use common\models\Account;
use common\models\Admin;
use common\models\Division;
use common\models\Location;
use yii\helpers\Url;
use rmrevin\yii\fontawesome\FA;
use phpDocumentor\Reflection\DocBlock\Description;
use common\widgets\inputs\assets\ICheckAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SectorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sectors');
$this->params['breadcrumbs'][] = $this->title;
$pageId = Yii::$app->controller->id;
$modelname = Sector::className();
$attributes = Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
$attributeLabels = $attributes['attributeLabels'];
?>
<div class="sector-index">

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
            <div
                class="button-container <?php echo (P::c(P::CONFIGURATIONS_SECTOR_PAGE_NEW)) ? '' : 'buttonpostion'; ?>">
                <?php if (P::c(P::CONFIGURATIONS_SECTOR_PAGE_NEW)) {
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
                            $url = \yii\helpers\Url::to(['sector/view', 'id' => $model->id]);
                            return \yii\helpers\Html::a($model->id, $url);
                        },
                    ],

                    [
                        'attribute' => 'name',
                        'visible' => !in_array('name', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'name'],
                    ],

                    [
                        'attribute' => 'code',
                        'visible' => !in_array('code', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'code'],
                    ],                    [
                        'attribute' => 'description',
                        'visible' => !in_array('description', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'description'],
                        'value' => function ($model) {
                            if (empty($model->description)) {
                                return '-';
                            } else {
                                return $model->description;
                            }
                        },
                    ],

                    [
                        'class' => RelationColumn::className(),
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminDivisionID() == Division::DIVISION_VILLA),
                        'attribute' => 'main_sector_id',
                        'visible' => !in_array('main_sector_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'main_sector_id'],
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->mainSector->name)) {
                                $link = ['main-sector/view', 'id' => $model->main_sector_id];
                                return Html::a($model->mainSector->name, $link);
                            }
                            return null;
                        },
                        'data' =>

                        Account::getAdminDivisionID() != '' ?
                            ArrayHelper::map(MainSector::find()->where(['division_id' => Yii::$app->user->identity->division_id])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name') :
                            ArrayHelper::map(MainSector::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()),
                        'visible' => !in_array('division_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'division_id'],
                        'attribute' => 'division_id',
                        'label' => 'Division',
                        'format' => 'html',
                        'value' => function ($model) {
                            return @$model->mainSector->division->name;
                        },
                        'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
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
                        'value'     => function (Sector $model) {

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
                        'value'     => function (Sector $model) {

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
                                $parentClass = 'common\models\Sector';
                                $parentId = $model->id;
                                $childClass = 'common\models\Location';
                                $paramsAttribute = 'sector_id';
                                $canMove = Yii::$app->user->can('management_deleted-entities_permission_move');
                                if ($model->status == Location::STATUS_DELETED) {
                                    if (P::c(P::CONFIGURATIONS_SECTOR_PAGE_DELETE)) {
                                        return Html::beginForm(['/dynamic/undelete', 'parentClass' => 'common\models\MainSector', 'parentId' => $model->main_sector_id, 'currentClass' => $parentClass, 'currentId' => $parentId, 'childClass' => $childClass, 'paramsAttribute' => $paramsAttribute], 'post') .
                                            Html::submitButton('Undelete', ['class' => 'btn btn-xs btn-warning', 'style' => ';margin-right:2px', 'data-confirm' => 'Are you sure you want to undelete this item?']) .
                                            Html::endForm();
                                    }
                                } else {
                                    if (P::c(P::CONFIGURATIONS_SECTOR_PAGE_DELETE)) {
                                        $buttonOptions = [
                                            'class' => 'btn btn-xs btn-danger delete-button',
                                            'data-parent-class' => $parentClass,
                                            'data-parent-id' => $parentId,
                                            'data-child-class' => $childClass,
                                            'data-child-name' => 'Location',
                                            'data-url-path' => Yii::$app->request->url,
                                            'data-parent-name' => 'Sector',
                                            'style' => 'min-width:57px;',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'data-params-attribute' => $paramsAttribute,
                                            'data-deleted-name' => $model->name,
                                        ];


                                        if ($canMove) {
                                            $buttonOptions['title'] = 'Make sure to delete or move all the location. Otherwise this sector will not be deleted.';
                                        }
                                        return Html::a('Delete', 'javascript:void(0);', $buttonOptions);
                                    }
                                }
                            },
                        ],

                        'permissions' => [
                            'view' => P::CONFIGURATIONS_SECTOR_PAGE_VIEW,
                            'update' => P::CONFIGURATIONS_SECTOR_PAGE_UPDATE,
                            'enable' =>  P::CONFIGURATIONS_SECTOR_PAGE_UPDATE,
                            'disable' =>  P::CONFIGURATIONS_SECTOR_PAGE_UPDATE,
                            'delete' =>  P::CONFIGURATIONS_SECTOR_PAGE_DELETE,
                            'audit' =>  P::CONFIGURATIONS_SECTOR_PAGE_AUDIT,
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
<?php $this->registerJsFile("@staticWeb/js/dynamic.js"); ?><?php ICheckAsset::register($this) ?>
<script>
<?php ob_start(); ?>
$('.icheck').iCheck({
    checkboxClass: 'icheckbox_square-green',
    increaseArea: '20%'
});
<?php $js = ob_get_clean(); ?>
<?php $this->registerJs($js); ?>
</script>