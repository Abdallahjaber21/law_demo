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
<div class="technician-index" style="padding:10px">

    <div class="row">
        <div class="col-md-12">
            <div style="background:#000;color:#fff;text-align:center">
                <?php $panel = PanelBox::begin([
                    'title' => '<span style="color:#fff">' . $this->title . '</span>',
                    'icon' => 'table',
                ]);
                ?>
            </div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [

                    [
                        'attribute' => 'image',
                        'visible' => !in_array('image', $hiddenAttributes),
                        'enableSorting' => false,
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
                        'enableSorting' => false,
                        'visible' => !in_array('id', $hiddenAttributes),
                    ],
                    [
                        'attribute' => 'name',
                        'enableSorting' => false,
                        'visible' => !in_array('name', $hiddenAttributes),
                    ],                   [
                        'attribute' => 'account_id',
                        'enableSorting' => false,
                        'visible' => !in_array('account_id', $hiddenAttributes),
                        'headerOptions' => ['class' => 'minus-header', 'data-attribute' => 'account_id'],
                        'value' => function ($model) {
                            return @$model->account->type0->label;
                        },


                    ],

                    [
                        'attribute' => 'division_id',
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) && !in_array('division_id', $hiddenAttributes),
                        'value' => function ($model) {

                            if (!empty($model->division_id))
                                return $model->division->name;
                        },
                        'format' => 'html',
                        'enableSorting' => false,

                    ],
                    [
                        'attribute' => 'main_sector_id',
                        'visible' => !in_array('main_sector_id', $hiddenAttributes),
                        'enableSorting' => false,
                        'value' => function ($model) {
                            if ($model->mainSector)
                                return "{$model->mainSector->name}";
                        },

                    ],

                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'country',
                        'enableSorting' => false,
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
                        'enableSorting' => false,
                    ],                    [
                        'attribute' => 'badge_number',
                        'visible' => !in_array('badge_number', $hiddenAttributes),
                        'enableSorting' => false,
                        'value' => function ($model) {

                            if (!empty($model->badge_number))
                                return Html::tag('div', $model->badge_number);
                        },
                        'format' => 'html'
                    ],


                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'profession_id',
                        'visible' => !in_array('profession_id', $hiddenAttributes),
                        'enableSorting' => false,
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->profession->name)) {
                                return $model->profession->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Profession::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],

                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'shift_id',
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
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'created_at',
                        'visible' => !in_array('created_at', $hiddenAttributes),
                        'enableSorting' => false,
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],
                    [
                        'attribute' => 'status',
                        'visible' => !in_array('status', $hiddenAttributes),
                        'enableSorting' => false,
                        'class' => common\components\extensions\OptionsColumn::class,

                    ],
                    [
                        'attribute' => 'work_status',
                        'value' => function ($model) {
                            return $model->getTechnicianWorkStatus(true);
                        },
                        'visible' => !in_array('work_status', $hiddenAttributes),
                        'enableSorting' => false,

                    ],

                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>