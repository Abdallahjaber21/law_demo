<?php

use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\components\extensions\RelationColumn;
use common\config\includes\P;
use common\models\Account;
use common\models\Admin;
use common\models\Category;
use common\models\Division;
use common\models\Equipment;
use common\models\EquipmentType;
use common\models\UserAudit;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use rmrevin\yii\fontawesome\FA;
use common\widgets\inputs\assets\ICheckAsset;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EquipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equipments';
$this->params['breadcrumbs'][] = $this->title;
$pageId = Yii::$app->controller->id;
$modelname = Equipment::className();
$attributes = common\models\Account::getHiddenAttributes($pageId, $modelname);
$hiddenAttributes = $attributes['attributeNames'];
$attributeLabels = $attributes['attributeLabels'];
?>

<div class="equipment-index" style="padding:10px">

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
                        'attribute' => 'id',
                        'visible' => !in_array('id', $hiddenAttributes),
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->id;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'name',
                        'visible' => !in_array('name', $hiddenAttributes),
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'code',
                        'visible' => !in_array('code', $hiddenAttributes),
                        'enableSorting' => false,
                    ],
                    [
                        'class' => RelationColumn::className(),
                        'attribute' => 'division_id',
                        'enableSorting' => false,
                        'format' => 'html',
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) && !in_array('division_id', $hiddenAttributes),
                        'value' => function ($model) {
                            if (!empty($model->division->name)) {
                                return $model->division->name;
                            }
                            return null;
                        },
                        'data' => ArrayHelper::map(Division::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
                        'attribute' => 'category_id',
                        'visible' => !in_array('category_id', $hiddenAttributes),
                        'enableSorting' => false,
                        'format' => 'html',
                        'value' => function ($model) {
                            if (!empty($model->category)) {
                                return $model->category->name;
                            }
                        },

                    ],
                    [
                        'attribute' => 'equipment_type_id',
                        'visible' => !in_array('equipment_type_id', $hiddenAttributes),
                        'enableSorting' => false,
                        'value' => function ($model) {
                            if ($model->equipmentType)
                                return !empty($model->equipmentType->code) ? $model->equipmentType->code . ' - ' . $model->equipmentType->name : $model->equipmentType->name;
                        },

                    ],

                    [
                        'attribute' => 'status',
                        'visible' => !in_array('status', $hiddenAttributes),
                        'enableSorting' => false,
                        'class' => common\components\extensions\OptionsColumn::class
                    ],
                    [
                        'attribute' => 'created_at',
                        'visible' => !in_array('created_at', $hiddenAttributes),
                        'enableSorting' => false,
                        'format' => 'datetime',
                        'class' => common\components\extensions\DateColumn::class
                    ],



                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>