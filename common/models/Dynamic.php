<?php

namespace common\models;

use yii\base\BaseObject;
use common\models\Sector;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii;
use yii\helpers\ArrayHelper;
use common\models\Division;

class Dynamic extends BaseObject
{
    public $parent;
    public $child;
    //deleting the parent modal 
    public function renderConfirmDeleteModal($parentClass)
    {
        $modalId = 'confirmDeleteModal';
        $formId = 'confirmDeleteForm';
        $html = Html::beginTag('div', ['class' => 'modal fade parent-modal', 'id' => $modalId]);
        $html .= Html::beginTag('div', ['class' => 'modal-dialog']);
        $html .= Html::beginTag('div', ['class' => 'modal-content']);
        $html .= Html::beginTag('div', ['class' => 'modal-header']);
        $html .= '<h4 class="modal-title">Are you sure you want to delete this item?</h4>';
        $html .= Html::button('×', ['class' => 'close', 'data-dismiss' => 'modal']);
        $html .= Html::endTag('div');
        $html .= Html::beginForm(['dynamic/update-parent-status'], 'post', ['id' => $formId]);
        $html .= Html::hiddenInput('classname', $this->parent, ['id' => 'confirmDeleteClassname']);
        $html .= Html::hiddenInput('child', $this->child, ['id' => 'confirmDeleteChild']);
        $html .= Html::hiddenInput('parentClass', $parentClass, ['id' => 'confirmDeleteParentClass']);
        $html .= Html::hiddenInput('parentID', null, ['id' => 'confirmDeleteParentID']);
        $html .= Html::hiddenInput('className', '', ['id' => 'confirmDeleteParentClass']);
        $html .= Html::hiddenInput('action', '', ['id' => 'confirmDeleteAction']);
        $html .= Html::beginTag('div', ['class' => 'modal-footer']);
        $html .= Html::button('Yes', [
            'class' => 'btn btn-danger', 'id' => 'confirmDeleteButton', 'name' => 'deleteParentButton',
        ]);
        $html .= Html::button('No', ['class' => 'btn btn-primary', 'data-dismiss' => 'modal']);
        $html .= Html::endTag('div');
        $html .= Html::endForm();
        $html .= Html::endTag('div');
        $html .= Html::endTag('div');
        $html .= Html::endTag('div');
        return $html;
    }
    //getting all the childs of a specific parent

    // Modify the getChildData method to join Equipment and select the necessary columns
    public function getChildData($parentID, $childClass, $parentAttribute)
    {
        $status = $childClass::STATUS_DELETED;

        if ($childClass == "common\models\LocationEquipments") {
            $childData = LocationEquipments::find()
                ->select(['location_equipments.*', 'equipment.name AS equipment_name', 'equipment.status AS equipment_status'])
                ->leftJoin('equipment', 'equipment.id = location_equipments.equipment_id')
                ->where(['location_id' => $parentID])
                ->andWhere(['<>', 'location_equipments.status', $status])
                ->orderBy('equipment_name')
                ->all();
        } else {
            $childData = $childClass::find()
                ->where([$parentAttribute => $parentID])
                ->andWhere(['<>', 'status', $status])
                ->orderBy('name')
                ->all();
        }

        return $childData;
    }

    // displaying all the childs in the new modal

    public function renderChildDataModal($parentClass, $childClass, $parentID, $parentAttribute)
    {
        $childData = $this->getChildData($parentID, $childClass, $parentAttribute);
        if (count($childData) == 0) {
        } else {
            $modalId = 'childModal_' . $parentID;
            $html = Html::beginTag('div', ['class' => 'modal fade childmodal', 'id' => $modalId]);
            $html .= Html::beginTag('div', ['class' => 'modal-dialog']);
            $html .= Html::beginTag('div', ['class' => 'modal-content']);
            $html .= Html::beginTag('div', ['class' => 'modal-header']);
            $html .= '<h4 class="modal-title">Child Data</h4>';
            $html .= Html::button('×', ['class' => 'close', 'data-dismiss' => 'modal']);
            $html .= Html::endTag('div');
            $html .= Html::beginForm(['dynamic/update-status'], 'post');
            $html .= Html::hiddenInput('className', $childClass);
            foreach ($childData as $child) {
                if ($childClass == "common\models\LocationEquipments") {
                    $childName = $child->equipment->name;
                } else {
                    $childName = $child->name;
                }

                if ($childClass == "common\models\LocationEquipments") {
                    $status = $child->status;
                    $parents = $parentClass::find()->select(['name', 'id'])->where(['<>', 'id', $parentID])->andWhere(['IN', 'location.sector_id', ArrayHelper::getColumn(Division::getSectors(Yii::$app->user->identity->division_id), 'id')])->andWhere([
                        '<>', 'status',
                        $status
                    ])->indexBy('id')->orderBy('name')->column();
                } else {
                    $status = $parentClass::STATUS_DELETED;
                    $parents = $parentClass::find()->select(['name', 'id'])->where(['<>', 'id', $parentID])->andWhere([
                        '<>', 'status',
                        $status
                    ])->indexBy('id')->orderBy('name')->column();
                }
                $childId = $child->id;
                $html .= Html::hiddenInput('childId', $childId);
                $status = $parentClass::STATUS_DELETED;

                $loggedMainSectorId = Yii::$app->user->identity->main_sector_id;

                $html .= Html::beginTag('div', ['class' => 'modal-body']);
                $html .= '<div class="child-row" style="display:flex;justify-content:space-between">';
                $html .= '<span>' . Html::encode($childName) . '</span>';
                $html .= Select2::widget([
                    'name' => 'parentDropdown[' . $childId . ']',
                    'data' => $parents,
                    'options' => ['placeholder' => 'Select'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '60%',
                    ],
                ]);
                $html .= Html::hiddenInput('attribute', $parentAttribute);
                $html .= Html::hiddenInput('child_id', $childId);
                $html .= Html::hiddenInput('newParentId', '', ['id' => 'newParentId_' . $childId]);
                $html .= Html::hiddenInput('paramAttributes', '', ['id' => 'paramAttributes_' . $childId]);
                $html .= Html::submitButton('Move', [
                    'class' => 'btn btn-primary move-child-button',
                    'data-child-id' => $childId,
                    'data-new-parent-id' => '',
                    'data-param-attributes' => $parentAttribute,
                    'data-child-class' => $childClass,
                    'name' => 'moveChildButton',
                ]);
                $html .= Html::submitButton('Delete', [
                    'class' => 'btn btn-xs btn-danger delete-child-button',
                    'name' => 'deleteChildButton',
                    'value' => $childId,
                    'child-data' => $childId,
                    'childId' => $childId,
                    'style' => 'min-width:57px',
                    'data-child-class' => $childClass,

                ]);

                $html .= '</div>';
                $html .= Html::endTag('div');
            }

            $html .= Html::endForm();
            $html .= Html::endTag('div');
            $html .= Html::endTag('div');
            $html .= Html::endTag('div');
            $html .= Html::endTag('div');
            return $html;
        }
    }
}
