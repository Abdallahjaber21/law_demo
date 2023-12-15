<?php

namespace common\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use common\models\UserAudit;

class UserAuditBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'logUserAction',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'logUserActionBeforeUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'logUserActionBeforeUpdate',

        ];
    }
    public function logUserAction($event)
    {
        $userAudit = new UserAudit();
        $userAudit->user_id = Yii::$app->user->id;
        $modelClassName = get_class($this->owner);
        switch ($modelClassName) {
            case 'common\models\Location':
                $userAudit->class_id = UserAudit::CLASS_NAME_LOCATION;
                break;
            case 'common\models\Equipment':
                $userAudit->class_id = UserAudit::CLASS_NAME_EQUIPMENT;
                break;
            case 'common\models\LocationEquipments':
                $userAudit->class_id = UserAudit::CLASS_NAME_LOCATIONEQUIPMENT;
                break;
            case 'common\models\Technician':
                $userAudit->class_id = UserAudit::CLASS_NAME_TECHNICIAN;
                break;
            case 'common\models\Admin':
                $userAudit->class_id = UserAudit::CLASS_NAME_ADMIN;
                break;
            case 'common\models\EquipmentType':
                $userAudit->class_id = UserAudit::CLASS_NAME_EQUIPMENTTYPE;
                break;
            case 'common\models\SegmentPath':
                $userAudit->class_id = UserAudit::CLASS_NAME_SEGMENTPATH;
                break;
            case 'common\models\Profession':
                $userAudit->class_id = UserAudit::CLASS_NAME_PROFESSION;
                break;
            case 'common\models\Category':
                $userAudit->class_id = UserAudit::CLASS_NAME_CATEGORY;
                break;
            case 'common\models\MainSector':
                $userAudit->class_id = UserAudit::CLASS_NAME_MAINSECTOR;
                break;
            case 'common\models\Sector':
                $userAudit->class_id = UserAudit::CLASS_NAME_SECTOR;
                break;
        }
        if ($event->name == ActiveRecord::EVENT_AFTER_INSERT) {
            $action = 'insert';
            $afterAttributes = $this->owner->getAttributes();
        }
        $statusBehavior = $this->owner->getBehavior('status');
        if ($statusBehavior !== null) {
            $afterAttributes['status'] = $statusBehavior->getOptionLabelByValue($afterAttributes['status']);
        }
        $afterAttributes['created_at'] = gmdate("Y-m-d H:i:s");
        $afterAttributes['updated_at'] = gmdate("Y-m-d H:i:s");
        $userAudit->action = $action;
        $userAudit->entity_row_id = $this->owner->id;
        $userAudit->old_value = '';
        $userAudit->new_value = json_encode($afterAttributes);

        if ($userAudit->save(false)) {

            Yii::info('UserAudit saved.');
        }
    }





    public function logUserActionBeforeUpdate($event)
    {
        $actionName = Yii::$app->controller->action->id;
        $oldAttributes = $this->owner->getOldAttributes();

        $newAttributes = $this->owner->getAttributes();
        $beforeAttributes = [];
        $afterAttributes = [];
        foreach ($newAttributes as $attribute => $newValue) {

            $oldValue = $oldAttributes[$attribute] ?? null;
            if ($this->valuesAreNotEqual($oldValue, $newValue)) {
                $beforeAttributes[$attribute] = $oldValue;
                $afterAttributes[$attribute] = $newValue;
                $statusBehavior = $this->owner->getBehavior('status');

                if ($statusBehavior !== null) {
                    if (!empty($beforeAttributes['status']) && !empty($afterAttributes['status'])) {
                        $beforeAttributes['status'] = $statusBehavior->getOptionLabelByValue($beforeAttributes['status']);
                        $afterAttributes['status'] = $statusBehavior->getOptionLabelByValue($afterAttributes['status']);
                    }
                }
            }
        }
        if ($actionName == 'delete') {
            $action = 'delete';
        } else {
            $action = 'update';
        }
        if (!empty($beforeAttributes)) {
            $userAudit = new UserAudit();
            $userAudit->user_id = Yii::$app->user->id;
            $modelClassName = get_class($this->owner);
            switch ($modelClassName) {
                case 'common\models\Location':
                    $userAudit->class_id = UserAudit::CLASS_NAME_LOCATION;
                    break;
                case 'common\models\Equipment':
                    $userAudit->class_id = UserAudit::CLASS_NAME_EQUIPMENT;
                    break;
                case 'common\models\LocationEquipments':
                    $userAudit->class_id = UserAudit::CLASS_NAME_LOCATIONEQUIPMENT;
                    break;
                case 'common\models\Admin':
                    $userAudit->class_id = UserAudit::CLASS_NAME_ADMIN;
                    break;
                case 'common\models\Technician':
                    $userAudit->class_id = UserAudit::CLASS_NAME_TECHNICIAN;
                    break;
                case 'common\models\EquipmentType':
                    $userAudit->class_id = UserAudit::CLASS_NAME_EQUIPMENTTYPE;
                    break;
                case 'common\models\SegmentPath':
                    $userAudit->class_id = UserAudit::CLASS_NAME_SEGMENTPATH;
                    break;
                case 'common\models\Profession':
                    $userAudit->class_id = UserAudit::CLASS_NAME_PROFESSION;
                    break;
                case 'common\models\Category':
                    $userAudit->class_id = UserAudit::CLASS_NAME_CATEGORY;
                    break;
                case 'common\models\MainSector':
                    $userAudit->class_id = UserAudit::CLASS_NAME_MAINSECTOR;
                    break;
                case 'common\models\Sector':
                    $userAudit->class_id = UserAudit::CLASS_NAME_SECTOR;
                    break;
            }


            $userAudit->action = $action;
            $userAudit->entity_row_id = $this->owner->id;
            $userAudit->old_value = json_encode($beforeAttributes);
            $userAudit->new_value = json_encode($afterAttributes);
            $userAudit->created_at = gmdate("Y-m-d H:i:s");
            if ($userAudit->save()) {
                Yii::info('UserAudit record saved.');
            }
        }
    }



    private function valuesAreNotEqual($value1, $value2)
    {
        $stringValue1 = (string)$value1;
        $stringValue2 = (string)$value2;
        return $stringValue1 !== $stringValue2;
    }
}
