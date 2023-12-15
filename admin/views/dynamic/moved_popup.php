<?php

use common\widgets\dashboard\PanelBox;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use rmrevin\yii\fontawesome\FA;
use common\models\Division;
use common\models\Account;
use common\models\Equipment;
use common\models\MainSector;


$this->title = !empty($childName) ? $childName : 'Dynamic Page';
$this->params['breadcrumbs'][] = ['label' => $parentName, 'url' =>  $urlPath];
$this->params['breadcrumbs'][] = ['label' => $deletedName];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="child-index">
    <?php $form = ActiveForm::begin([
        'id' => 'move_action',
    ]); ?>
    <?php if ($dataProvider->getCount() > 0) { ?>

    <div class="row" style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
        <div class="col-sm-3">
            <?php if ($parentClass == 'common\models\Location') {
                    if ($childClass == 'common\models\LocationEquipments') {
                        $status = $parentClass::STATUS_ENABLED;
                        if (Account::getAdminTypeID() == Account::SUPER_ADMIN) {
                            $parentDropdown   = $parentClass::find()
                                ->select(['name', 'id'])
                                ->where(['<>', 'id', $parentId])
                                ->andWhere(['=', 'status', $status])
                                ->indexBy('id')
                                ->orderBy('name')
                                ->column();
                        } else {
                            $parentDropdown   = $parentClass::find()
                                ->select(['name', 'id'])
                                ->where(['<>', 'id', $parentId])
                                ->andWhere(['IN', 'sector_id', @ArrayHelper::getColumn(@Division::getSectors(Yii::$app->user->identity->division_id), 'id')])
                                ->andWhere(['=', 'status', $status])
                                ->indexBy('id')
                                ->orderBy('name')
                                ->column();
                        }
                    }
                } else if ($parentClass == 'common\models\Sector') {
                    $status = $parentClass::STATUS_ENABLED;
                    if (Account::getAdminTypeID() == Account::SUPER_ADMIN) {
                        $parentDropdown = [];

                        $sectors = MainSector::getAllSectors($parentId);

                        if ($sectors !== null) {
                            $status = $parentClass::STATUS_ENABLED;

                            $parentDropdown = $parentClass::find()
                                ->select(['name', 'id'])
                                ->where(['<>', 'id', $parentId])
                                ->andWhere(['IN', 'id', ArrayHelper::getColumn($sectors, 'id')])
                                ->andWhere(['=', 'status', $status])
                                ->indexBy('id')
                                ->orderBy('name')
                                ->column();
                        }
                    } else {
                        $parentDropdown = [];

                        $sectors = MainSector::getAllSectors(Yii::$app->user->identity->main_sector_id);

                        if ($sectors !== null) {
                            $status = $parentClass::STATUS_ENABLED;

                            $parentDropdown = $parentClass::find()
                                ->select(['name', 'id'])
                                ->where(['<>', 'id', $parentId])
                                ->andWhere(['IN', 'id', ArrayHelper::getColumn($sectors, 'id')])
                                ->andWhere(['=', 'status', $status])
                                ->indexBy('id')
                                ->orderBy('name')
                                ->column();
                        }
                    }
                } else if (($parentClass == 'common\models\AccountType')) {
                    if ($childClass == 'common\models\Admin') {
                        $parentDropdown = ArrayHelper::map($parentClass::find()
                            ->where(['=', 'status', $parentClass::STATUS_ENABLED])
                            ->andWhere(['<>', 'id', $parentId])
                            ->andWhere(['for_backend' => 1])
                            ->all(), 'id', 'name');
                    } else {
                        $parentDropdown = ArrayHelper::map($parentClass::find()
                            ->where(['=', 'status', $parentClass::STATUS_ENABLED])
                            ->andWhere(['<>', 'id', $parentId])
                            ->andWhere(['for_backend' => 0])
                            ->all(), 'id', 'name');
                    }
                } else {
                    $parentDropdown = ArrayHelper::map($parentClass::find()
                        ->where(['=', 'status', $parentClass::STATUS_ENABLED])
                        ->andWhere(['<>', 'id', $parentId])
                        ->all(), 'id', 'name');
                }
                ?>
            <?= Select2::widget([
                    'name' => 'newParentId',
                    'id' => 'parent-dropdown',
                    'data' => $parentDropdown,
                    'options' => ['placeholder' => 'Select new ' . $parentName],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]); ?>
        </div>
        <?= Html::submitButton(FA::i(FA::_EXCHANGE) . ' Move', ['class' => 'btn btn-primary', 'style' => 'float:right', 'name' => 'moveButton', 'id' => 'moveButton', 'disabled' => true]) ?>
        <?= Html::submitButton(FA::i(FA::_TRASH) . ' Delete', ['class' => 'btn btn-danger', 'style' => 'float:right;margin-right:10px', 'name' => 'deleteButton', 'id' => 'deleteButton', 'disabled' => true]) ?>
    </div>
    <?php } ?>

    <br />
    <?php

    $panel = PanelBox::begin([
        'title' => Html::encode($this->title),
        'icon' => 'eye',
        'color' => PanelBox::COLOR_BLUE
    ]);

    $panel->beginHeaderItem();
    ?>

    <?php $panel->endHeaderItem(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return ['value' => $model->id];
                },
            ],
            [
                'attribute' => 'name',
                'label' => $parentClass == 'common\models\Location' ? 'Equipment Name' : 'Child Name',
                'value' => function ($model) use ($parentClass) {
                    if ($parentClass == 'common\models\Location') {
                        return $model->equipment->name;
                    } else {
                        return $model->name;
                    }
                },
            ],
            ($parentClass == 'common\models\Location' || $parentClass == 'common\models\Category') ?
                [
                    'attribute' => 'code',
                    'label' => 'Code',
                    'value' => function ($model) {
                        return $model->code;
                    },
                ] : [
                    'attribute' => 'updated_at',
                    'label' => 'Updated At',
                    'value' => function ($model) {
                        return $model->updated_at;
                    },
                ],
            [
                'attribute' => 'path',

                'label' => 'Equipment Location Path',
                'visible' => $parentClass == 'common\models\Location',
                'value' => function ($model) {
                    if (!empty($model)) {
                        return Equipment::getLayersValue($model->value);
                    }
                },
                'format' => 'raw'
            ],
            ($parentClass == 'common\models\Location') ?
                [
                    'attribute' => 'custom_attributes',
                    'label' => 'Attributes',
                    'value' => function ($model) {
                        return Equipment::getEquipmentCustomAttributes($model->equipment_id, $model->id);
                    },
                    'format' => 'raw',
                    'filter' => false
                ] :
                [
                    'attribute' => 'STATUS',
                    'label' => 'Status',
                    'value' => function ($model) {
                        return $model->status_label;
                    },
                ],

        ],
    ]);
    $panel->end();
    ?>


    <?php ActiveForm::end(); ?>

</div>
<style>
.grid-view tr td:last-child {
    text-align: left !important;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php $this->registerJsFile("@staticWeb/js/dynamic.js"); ?>