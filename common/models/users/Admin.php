<?php

namespace common\models\users;

use common\models\AdminSector;
use common\models\Location;
use common\models\Sector;
use common\models\Technician;
use common\models\TechnicianSector;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Description of Admin
 *
 * @author Tarek K. Ajaj
 * Apr 4, 2017 7:09:15 PM
 *
 * Admin.php
 * UTF-8
 *
 *
 * @property AdminSector[] $adminSectors
 * @property Sector[] $sectors
 *
 */
class Admin extends AbstractAccount
{
    const STATUS_ENABLED = 20;
    const STATUS_DELETED = 30;
    const STATUS_DISABLED = 10;

    /**
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{admin}}";
    }

    /**
     * @inheritdoc
     */
    // public function rules()
    // {
    //     return array_merge(parent::rules(), [
    //         ['email', 'unique', 'targetClass' => Admin::className()],
    //     ]);
    // }

    /**
     *
     * @inheritdoc
     */
    public function getUserType()
    {
        return 'admin';
    }

    /**
     * @return ActiveQuery
     */
    public function getAdminSectors()
    {
        return $this->hasMany(AdminSector::className(), ['admin_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSectors()
    {
        return $this->hasMany(Sector::className(), ['id' => 'sector_id'])->viaTable('admin_sector', ['admin_id' => 'id']);
    }

    public function sectorsIds()
    {
        return ArrayHelper::getColumn($this->adminSectors, 'sector_id', false);;
    }

    public static function activeSectorsIds()
    {
        return \Yii::$app->user->identity->sectorsIds();
    }

    public static function sectorsKeyValList()
    {
        return ArrayHelper::map(Sector::find()
            ->filterWhere(['id' => Admin::activeSectorsIds()])
            ->orderBy(['code' => SORT_ASC])->all(), 'id', 'code');
    }

    public static function techniciansKeyValList()
    {
        return ArrayHelper::map(Technician::find()
            ->joinWith(['technicianSectors'])
            ->filterWhere([TechnicianSector::tableName() . '.sector_id' => self::activeSectorsIds()])
            ->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }

    public static function locationsKeyValList()
    {
        return ArrayHelper::map(Location::find()
            ->filterWhere(['sector_id' => self::activeSectorsIds()])
            ->all(), 'id', function ($model) {
            return "{$model->name} - {$model->code}";
        });
    }
}
