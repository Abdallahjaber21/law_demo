<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "equipment_ca".
 *
 * @property int $id
 * @property int $division_id
 * @property string $name
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Division $division
 * @property EquipmentCaValue[] $equipmentCaValues
 */
class EquipmentCa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equipment_ca';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['division_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'division_id' => 'Division ID',
            'name' => 'Name',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        return $this->hasOne(Division::className(), ['id' => 'division_id']);
    }
    public static function getEquipmentCustomAttributeDivisionCount($division_id)
    {
        $equipmentCa = EquipmentCa::find()->where(['division_id' => $division_id])->all();

        return count($equipmentCa);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentCaValues()
    {
        return $this->hasMany(EquipmentCaValue::className(), ['equipment_ca_id' => 'id']);
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'division_id',
            'equipment_id' => function ($model) {
                return 0;
            }
        ];
    }
}
