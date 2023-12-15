<?php

namespace common\components\extensions;

use common\models\UserAudit;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Description of MyActionColumn
 *
 * @author Tarek K. Ajaj
 * Feb 23, 2017 1:33:51 PM
 * 
 * MyActionColumn.php
 * UTF-8
 * 
 */
class ActionColumn extends \yii\grid\ActionColumn
{

    public $permissions = [];

    public function init()
    {
        if (empty($this->visibleButtons)) {
            $this->visibleButtons = [
                'disable' => function ($model, $key, $index) {
                    return $model->status == 20;
                },
                'enable' => function ($model, $key, $index) {
                    return $model->status == 10;
                },
            ];
        }
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view']) && $this->checkPermission('view')) {
            $this->buttons['view'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t("app", 'View'),
                    'aria-label' => Yii::t("app", 'View'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-primary'
                ], $this->buttonOptions);
                return Html::a(\Yii::t("app", 'View'), $url, $options);
            };
        }
        if (!isset($this->buttons['update']) && $this->checkPermission('update')) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t("app", 'Update'),
                    'aria-label' => Yii::t("app", 'Update'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs bg-purple'
                ], $this->buttonOptions);
                return Html::a(\Yii::t("app", 'Edit'), $url, $options);
            };
        }
        if (!isset($this->buttons['delete']) && $this->checkPermission('delete')) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t("app", 'Delete'),
                    'aria-label' => Yii::t("app", 'Delete'),
                    'data-confirm' => Yii::t("app", 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-danger'
                ], $this->buttonOptions);
                return Html::a(\Yii::t("app", 'Delete'), $url, $options);
            };
        }
        if (!isset($this->buttons['audit']) && $this->checkPermission('audit')) {
            $this->buttons['audit'] = function ($url, $model, $key) {
                if (Yii::$app->controller->id == 'location') {
                    $class_id = UserAudit::CLASS_NAME_LOCATION;
                } else if (Yii::$app->controller->id == 'equipment') {
                    $class_id = UserAudit::CLASS_NAME_EQUIPMENT;
                } else if (Yii::$app->controller->id == 'location-equipments') {
                    $class_id = UserAudit::CLASS_NAME_LOCATIONEQUIPMENT;
                } else if (Yii::$app->controller->id == 'technician') {
                    $class_id = UserAudit::CLASS_NAME_TECHNICIAN;
                } else if (Yii::$app->controller->id == 'equipment-type') {
                    $class_id = UserAudit::CLASS_NAME_EQUIPMENTTYPE;
                } else if (Yii::$app->controller->id == 'segment-path') {
                    $class_id = UserAudit::CLASS_NAME_SEGMENTPATH;
                } else if (Yii::$app->controller->id == 'profession') {
                    $class_id = UserAudit::CLASS_NAME_PROFESSION;
                } else if (Yii::$app->controller->id == 'category') {
                    $class_id = UserAudit::CLASS_NAME_CATEGORY;
                } else if (Yii::$app->controller->id == 'main-sector') {
                    $class_id = UserAudit::CLASS_NAME_MAINSECTOR;
                } else if (Yii::$app->controller->id == 'sector') {
                    $class_id = UserAudit::CLASS_NAME_SECTOR;
                } else if (Yii::$app->controller->id == 'admin') {
                    $class_id = UserAudit::CLASS_NAME_ADMIN;
                }
                $url = Url::to(['user-audit/index', 'class_id' => $class_id, 'entity_row_id' => $key]);
                $options = array_merge([
                    'title' => Yii::t("app", 'Audit'),
                    'aria-label' => Yii::t("app", 'Audit'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs bg-yellow'
                ], $this->buttonOptions);
                return Html::a(\Yii::t("app", 'Audit'), $url, $options);
            };
        }
        if (!isset($this->buttons['disable']) && $this->checkPermission('disable')) {
            $this->buttons['disable'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t("app", 'Disable'),
                    'aria-label' => Yii::t("app", 'Disable'),
                    'data-confirm' => Yii::t("app", 'Are you sure you want to Disable this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-danger'
                ], $this->buttonOptions);
                return Html::a(\Yii::t("app", 'Disable'), $url, $options);
            };
        }
        if (!isset($this->buttons['enable']) && $this->checkPermission('enable')) {
            $this->buttons['enable'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t("app", 'Enable'),
                    'aria-label' => Yii::t("app", 'Enable'),
                    'data-confirm' => Yii::t("app", 'Are you sure you want to Enable this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-xs btn-success'
                ], $this->buttonOptions);
                return Html::a(\Yii::t("app", 'Enable'), $url, $options);
            };
        }
    }

    protected function checkPermission($action)
    {
        if (isset($this->permissions[$action])) {
            return Yii::$app->user->can($this->permissions[$action]);
        }
        return true; // No permission specified, allow by default
    }
}
