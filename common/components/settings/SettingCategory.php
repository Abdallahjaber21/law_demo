<?php

namespace common\components\settings;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Description of SettingCategory
 *
 * @author Tarek K. Ajaj
 * Apr 7, 2017 11:14:09 AM
 * 
 * SettingCategory.php
 * UTF-8
 * 
 * @property integer $id
 * @property string $name
 * @property string $label
 * @property string $description
 * 
 * @property Setting[] $settings
 */
class SettingCategory extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return "{{setting_category}}";
    }

    /*
     * @inheritdoc
     */

    public function rules() {
        return [
            //--- Name
            ['name', 'required'],
            ['name', 'string'],
            //--- Label
            ['label', 'required'],
            ['label', 'string'],
            //--- Description
            ['description', 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t("app", 'ID'),
            'name' => Yii::t("app", 'Name'),
            'label' => Yii::t("app", 'Label'),
            'description' => Yii::t("app", 'Description'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getSettings() {
        return $this->hasMany(Setting::className(), ['setting_category_id' => 'id']);
    }

    /**
     * find a setting category by it's name
     * @param string $name
     */
    public static function findByName($name) {
        return self::findOne(['name' => $name]);
    }

    /**
     * 
     * @param string $name
     * @param array $config
     * @return \common\components\settings\SettingCategory
     */
    public static function addNew($name, $config) {
        $category = new SettingCategory();
        $category->name = $name;
        $category->label = $config['label'];
        $category->description = $config['description'];
        $category->save();
        return $category;
    }

}
