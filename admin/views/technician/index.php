<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\data\Countries;
use common\models\Account;
use yii\grid\GridView;
use common\models\Division;
use common\models\MainSector;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\Profession;
use common\models\Shift;
use common\models\Technician;
use common\models\TechnicianShift;
use common\models\Country;
use rmrevin\yii\fontawesome\FA;
use yii\widgets\ActiveForm;
use common\widgets\inputs\assets\ICheckAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TechnicianSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Technicians';
$this->params['breadcrumbs'][] = $this->title;
$pageId = Yii::$app->controller->id;
$modelname = Technician::className();
$attributes = common\models\Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
$attributeLabels = $attributes['attributeLabels'];
?>
<div class="technician-index">


    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <div class="button-container <?php echo (P::c(P::MANAGEMENT_TECHNICIAN_PAGE_NEW)) ? '' : 'buttonpostion'; ?>">
                <?php if (P::c(P::MANAGEMENT_TECHNICIAN_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['create'], ['class' => 'btn btn-primary btn-flat', 'style' => 'margin-right:10px']);
                }
                $panel->addButton(Yii::t('app', FA::i(FA::_EDIT) . ' Edit'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'style' => 'margin-right:10px', 'id' => 'show-buttons']);
                $panel->addButton(Yii::t('app', 'Cancel'), 'javascript:void(0);', ['class' => 'btn btn-warning', 'style' => 'margin-right:10px;display:none', 'id' => 'cancel_btn']);
                ?>
                <?php if ((P::c(P::MANAGEMENT_TECHNICIAN_PAGE_EXPORT))) { ?>
                    <div class="form-group ">
                        <?= Html::beginForm(['technician/index'] + Yii::$app->request->queryParams, 'GET', ['class' => 'form-inline', 'id' => 'date-range-form']) ?>
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
                        'attribute' => 'image',
                        'visible' => !in_array('image', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'image'],
                        'value' => function ($model) {
                            $imagePath =  $model->image_thumb_path;
                            if (file_exists($imagePath)) {
                                $imageUrl = $model->image_thumb_url;
                            } else {
                                $imageUrl = Yii::getAlias('@staticWeb') . '/images/user-default.jpg';
                            }
                            return Html::img($imageUrl, ['alt' => $model->image, 'width' => '60', 'style' => [
                                'border-radius' => '50%',
                                'margin-left' => 'auto',
                            ]]);
                        },
                        'contentOptions' => ['style' => 'text-align:center;'],
                        'format' => 'raw',
                        'filter' => false
                    ],
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:75px', 'class' => 'minus-header', 'data-attribute' => 'id'],
                        'visible' => !in_array('id', $hiddenAttributes),
                        'format' => 'raw',
                        'value' => function ($model) {
                            $url = \yii\helpers\Url::to(['technician/view', 'id' => $model->id]);
                            return \yii\helpers\Html::a($model->id, $url);
                        },
                    ],

                    [
                        'attribute' => 'name',
                        'visible' => !in_array('name', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'name']
                    ],                   [
                        'attribute' => 'account_id',
                        'visible' => !in_array('account_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'account_id'],
                        'value' => function ($model) {
                            return @$model->account->type0->label;
                        },
                        // 'class' => common\components\extensions\OptionsColumn::class
                        'filter'    => Select2::widget([
                            'model'         => $searchModel,
                            'attribute'     => 'type',
                            'data'          => Account::getTechnicianOptions(),
                            'pluginOptions' => [
                                'multiple'   => false,
                                'allowClear' => true
                            ],
                            'options'       => [
                                'placeholder' => ''
                            ],
                        ])

                    ],
                    // [
                    //     'attribute' => 'email',
                    //     'visible' => !in_array('email', $hiddenAttributes),
                    //     'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'email']
                    // ],
                    [
                        'attribute' => 'division_id',
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'division_id'],
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) && !in_array('division_id', $hiddenAttributes),
                        'value' => function ($model) {

                            if (!empty($model->division_id))
                                return $model->division->name;
                        },
                        'format' => 'html',
                        'enableSorting' => true,
                        'filter'    => Select2::widget([
                            'model'         => $searchModel,
                            'attribute'     => 'superadmin_division_id',
                            'data'          => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                            'pluginOptions' => [
                                'multiple'   => false,
                                'allowClear' => true
                            ],
                            'options'       => [
                                'placeholder' => ''
                            ],
                        ])
                    ],
                    [
                        'attribute' => 'main_sector_id',
                        'visible' => !in_array('main_sector_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'main_sector_id'],
                        'value' => function ($model) {
                            if ($model->mainSector)
                                return "{$model->mainSector->name}";
                        },
                        'filter'    => empty(Account::getAdminAccountTypeDivisionModel()) ? Select2::widget([
                            'model'         => $searchModel,
                            'attribute'     => 'main_sector_id',
                            'data'          => ArrayHelper::map(MainSector::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                            'pluginOptions' => [
                                'multiple'   => false,
                                'allowClear' => true
                            ],
                            'options'       => [
                                'placeholder' => ''
                            ],
                        ]) : false
                    ],

                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'country',
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'country'],
                        'format' => 'html',
                        'visible' => !in_array('country', $hiddenAttributes),
                        'value' => function ($model) {
                            return Countries::getCountryName($model->country);
                        },
                        'data' =>  Countries::getCountriesList()
                    ],
                    [
                        'attribute' => 'phone_number',
                        'visible' => !in_array('phone_number', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'phone_number'],
                    ],                    [
                        'attribute' => 'badge_number',
                        'visible' => !in_array('badge_number', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'badge_number'],
                        'value' => function ($model) {

                            if (!empty($model->badge_number))
                                return Html::tag('div', $model->badge_number, ['class' => 'badge badge-warning']);
                        },
                        'format' => 'html'
                    ],


                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'profession_id',
                        'visible' => !in_array('profession_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'profession_id'],
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->profession->name)) {
                                $link = ['profession/view', 'id' => $model->profession_id];
                                return Html::a($model->profession->name, $link);
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Profession::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],

                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'shift_id',
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'shift_id'],
                        'visible' => !in_array('shift_id', $hiddenAttributes) && (empty(Yii::$app->user->identity->division_id) || (Yii::$app->user->identity->division->has_shifts)),

                        'format' => 'html',
                        'value' => function ($model) {
                            $model->shift_id;
                            $date = date('Y-m-d');
                            $data = TechnicianShift::find()
                                ->where(
                                    [
                                        'AND',
                                        ['technician_id' => $model->id],
                                        ['date' => $date]
                                    ]
                                )
                                ->one();

                            if (!empty($data)) {
                                return $data->shift->name;
                            }
                        },
                        'data' => ArrayHelper::map(Shift::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        'enableSorting' => true
                    ],
                    [
                        'attribute' => 'created_at',
                        'visible' => !in_array('created_at', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'created_at'],
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],
                    [
                        'attribute' => 'status',
                        'visible' => !in_array('status', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'status'],
                        'class' => common\components\extensions\OptionsColumn::class,
                        'enableSorting' => true

                    ],
                    [
                        'attribute' => 'work_status',
                        'value' => function ($model) {
                            return $model->getTechnicianWorkStatus(true);
                        },
                        'visible' => !in_array('work_status', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'work_status'],

                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'attribute' => 'work_status',
                            'value' => Yii::$app->request->get('work_status'),
                            'data' => Technician::getTechnicianWorkStatuses(),
                            'options' => ['multiple' => false, 'placeholder' => 'Select a work status'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ]
                        ])
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete} {audit}',
                        'headerOptions' => ['style' => 'width:100px;white-space: nowrap;'],
                        'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                if (P::c(P::MANAGEMENT_TECHNICIAN_PAGE_DELETE)) {
                                    if ($model->status == Technician::STATUS_DELETED) {
                                        return Html::a('Undelete', $url, [
                                            'title' => Yii::t('yii', 'Undelete'),
                                            'class' => 'btn btn-xs btn-warning',
                                            'style' => 'min-width:53px;',
                                            'data-method' => 'post',
                                            'data-confirm' => 'Are you sure you want to undelete this item?',
                                        ]);
                                    } else {
                                        $confirmMessage = 'Are you sure you want to delete this item?';
                                        return Html::a('Delete', $url, [
                                            'title' => Yii::t('yii', 'Delete'),
                                            'class' => 'btn btn-xs btn-danger',
                                            'style' => 'min-width:53px;',
                                            'data-method' => 'post',
                                            'data-confirm' => $confirmMessage,
                                        ]);
                                    }
                                }
                            },
                        ],
                        'permissions' => [
                            'view' => P::MANAGEMENT_TECHNICIAN_PAGE_VIEW,
                            'update' => P::MANAGEMENT_TECHNICIAN_PAGE_UPDATE,
                            'enable' =>  P::MANAGEMENT_TECHNICIAN_PAGE_UPDATE,
                            'disable' =>  P::MANAGEMENT_TECHNICIAN_PAGE_UPDATE,
                            'delete' => P::MANAGEMENT_TECHNICIAN_PAGE_DELETE,
                            'audit' => P::MANAGEMENT_TECHNICIAN_PAGE_AUDIT,

                        ],
                    ],

                    [
                        'format' => 'raw',
                        'value' => function ($model) {

                            if ($model->division->has_shifts &&  (P::c(P::MANAGEMENT_TECHNICIAN_SHIFTS_PAGE_VIEW))) {
                                $url = ['technician-shift/index',  'month' => date('n'), 'year' => date('Y'), 'technician_id' => $model->id];
                                return Html::a(
                                    'View Shifts',
                                    $url,
                                    ['class' => 'btn btn-xs btn-success']
                                );
                            }

                            return '';
                        },
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
        radioClass: 'icheckbox_square-blue',
        increaseArea: '20%'
    });
    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>