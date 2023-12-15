<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "unit_type".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 */
class UnitType extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unit_type';
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
    public function behaviors()
    {
        return [
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
