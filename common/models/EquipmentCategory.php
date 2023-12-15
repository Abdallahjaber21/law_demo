<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "equipment_category".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 */
class EquipmentCategory extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipment_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key', 'name'], 'string', 'max' => 255],
            [['key'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'   => 'ID',
            'key'  => 'Key',
            'name' => 'Name',
        ];
    }
}
