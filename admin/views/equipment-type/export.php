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

$this->title = 'Equipment Types';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="equipment-type-index" style="padding:10px">
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
                        'headerOptions' => ['style' => 'width:75px'],
                        'value' => function ($model) {
                            return $model->id;
                        },
                        'enableSorting' => false,
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'code',
                        'enableSorting' => false,

                    ],
                    [
                        'attribute' => 'name',
                        'enableSorting' => false,

                    ],
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
                        'enableSorting' => false,

                        'data' => ArrayHelper::map(Category::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name')
                    ],
                    [
                        'class' => common\components\extensions\OptionsColumn::class,
                        'attribute' => 'meter_type',
                        'enableSorting' => false,
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
                        'enableSorting' => false,
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminAccountTypeDivisionModel()->id == Division::DIVISION_PLANT)
                    ],
                    [
                        'attribute' => 'equivalance',
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->equivalance;
                        },
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminAccountTypeDivisionModel()->id == Division::DIVISION_PLANT)
                    ],
                    [
                        'attribute' => 'status',
                        'enableSorting' => false,
                        'class' => common\components\extensions\OptionsColumn::class
                    ],

                ],
            ]); ?>

            <?php PanelBox::end() ?>
        </div>
    </div>
</div>