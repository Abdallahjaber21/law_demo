<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Account;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\Equipment;
use common\models\EquipmentType;
use common\models\Category;
use common\models\Division;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EquipmentTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equipment Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-type-index">

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
            <div class="button-container <?php echo (P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_NEW)) ? '' : 'buttonpostion'; ?>"
                style="<?php echo (P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_NEW)) ? 'right:65px' : 'right:10px'; ?>">

                <?php if ((P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_EXPORT))) { ?>
                <div class="form-group ">
                    <?= Html::beginForm(['equipment-type/index']+ Yii::$app->request->queryParams, 'GET', ['class' => 'form-inline', 'id' => 'date-range-form']) ?>
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
                </div>
                <?php } ?>
            </div>
            <?php if (P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_NEW)) {
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
                        'value' => function ($model) {
                            return Html::a($model->id, Url::to(['view', 'id' => $model->id]));
                        },
                        'format' => 'raw'
                    ],
                    'code',
                    'name',
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'category_id',
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->category->name)) {
                                $link = ['category/view', 'id' => $model->category_id];
                                return $model->category->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Category::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
                        'class' => common\components\extensions\OptionsColumn::class,
                        'attribute' => 'meter_type',
                        'value' => function ($model) {
                            return $model->meter_type_label;
                        },
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminAccountTypeDivisionModel()->id == Division::DIVISION_PLANT)
                    ],
                    [
                        'class' => common\components\extensions\OptionsColumn::class,
                        'attribute' => 'alt_meter_type',
                        'value' => function ($model) {
                            return $model->alt_meter_type_label;
                        },
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminAccountTypeDivisionModel()->id == Division::DIVISION_PLANT)
                    ],
                    [
                        'attribute' => 'reference_value',
                        'value' => function ($model) {
                            return $model->reference_value;
                        },
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminAccountTypeDivisionModel()->id == Division::DIVISION_PLANT)
                    ],
                    [
                        'attribute' => 'equivalance',
                        'value' => function ($model) {
                            return $model->equivalance;
                        },
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminAccountTypeDivisionModel()->id == Division::DIVISION_PLANT)
                    ],
                    [
                        'attribute' => 'status',
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete} {audit}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                $parentClass = 'common\models\EquipmentType';
                                $parentId = $model->id;
                                $childClass = 'common\models\Equipment';
                                $paramsAttribute = 'equipment_type_id';
                                $canMove = Yii::$app->user->can('management_deleted-entities_permission_move');
                                if ($model->status == EquipmentType::STATUS_DELETED) {
                                    if (P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_DELETE)) {
                                        return Html::beginForm(['/dynamic/undelete', 'parentClass' => 'common\models\Category', 'parentId' => $model->category_id, 'currentClass' => $parentClass, 'currentID' => $parentId, 'childClass' => $childClass, 'paramsAttribute' => $paramsAttribute], 'post') .
                                            Html::submitButton('Undelete', ['class' => 'btn btn-xs btn-warning', 'data-confirm' => 'Are you sure you want to undelete this item?']) .
                                            Html::endForm();
                                    }
                                } else {
                                    if (P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_DELETE)) {
                                        $buttonOptions = [
                                            'class' => 'btn btn-xs btn-danger delete-button',
                                            'data-parent-class' => $parentClass,
                                            'data-parent-id' => $parentId,
                                            'data-child-class' => $childClass,
                                            'data-child-name' => 'Equipment ',
                                            'data-url-path' => Yii::$app->request->url,
                                            'data-parent-name' => 'Equipment Type',
                                            'style' => 'min-width:57px;',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'data-params-attribute' => $paramsAttribute,
                                            'data-deleted-name' => $model->name,
                                        ];

                                        if ($canMove) {
                                            $buttonOptions['title'] = 'Make sure to delete or move all the equipment. Otherwise this equippment type will not be deleted.';
                                        }
                                        return Html::a('Delete', 'javascript:void(0);', $buttonOptions);
                                    }
                                }
                            },
                        ],
                        'permissions' => [
                            'view' => P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_VIEW,
                            'update' => P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_UPDATE,
                            'enable' => P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_UPDATE,
                            'disable' => P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_UPDATE,
                            'delete' => P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_DELETE,
                            'audit' => P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_AUDIT,
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