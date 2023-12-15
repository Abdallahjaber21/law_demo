<?php

use common\config\includes\P;
use common\models\Account;
use common\models\Division;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Equipment Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-type-view">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php if (P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_UPDATE)) { ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php } ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'code',
                    'name',

                    [
                        'attribute' => 'category_id',
                        'value' => @Html::tag("p", $model->category->name),
                        'format' => 'html',

                    ],
                    [

                        'attribute' => 'meter_type',
                        'value' => function ($model) {
                            return $model->meter_type_label;
                        },
                        'visible' => empty(Account::getAdminAccountTypeDivisionModel()) || (Account::getAdminAccountTypeDivisionModel()->id == Division::DIVISION_PLANT)
                    ],
                    [

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
                ],
            ]) ?>

            <?php PanelBox::end() ?>
        </div>
        <?php
        $dataprovider = new ArrayDataProvider(
            [
                'allModels' => $model->equipments,
            ]
        ); ?>
        <?php if (P::C(P::MANAGEMENT_EQUIPMENT_PAGE_VIEW)) : ?>
            <div class="col-md-12">
                <?php
                $panel = PanelBox::begin([
                    'title' => Html::encode('Equipments'),
                    'icon' => 'eye',
                    'color' => PanelBox::COLOR_GREEN
                ]);
                ?>
                <?php if (P::c(P::MANAGEMENT_EQUIPMENT_PAGE_NEW)) {
                    $panel->addButton(Yii::t('app', 'New'), ['equipment/create', 'division_id' => Yii::$app->user->identity->division_id, 'category_id' => $model->category->id, 'equipment_type_id' => $model->id], ['class' => 'btn btn-primary btn-flat']);
                }
                ?>
                <?php if (!empty($model->equipments)) : ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataprovider,
                        'filterModel' => null,
                        'columns' => [
                            [
                                'attribute' => 'name',
                                'value' => function ($model) {
                                    return $model->name;
                                }
                            ],
                            'code',
                            [
                                'attribute' => 'status',
                                'class' => common\components\extensions\OptionsColumn::class
                            ],



                        ],
                    ]); ?>
                <?php else : ?>
                    <h3 class="no-data">No Data</h3>
                <?php endif; ?>
                <?php PanelBox::end() ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<style>
    .grid-view tr td:last-child {
        text-align: left
    }
</style>