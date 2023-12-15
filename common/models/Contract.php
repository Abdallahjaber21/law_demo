<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "contract".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $code
 * @property string $name
 * @property int $includes_parts
 * @property int $same_day_service
 * @property double $same_day_cost
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property string $expire_at
 * @property string $material
 * @property int $temporary_in
 * @property int $temporary_out
 *
 * @property Customer $customer
 * @property Equipment[] $equipments
 */
class Contract extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contract';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'includes_parts', 'same_day_service', 'status', 'created_by', 'updated_by', 'temporary_in', 'temporary_out'], 'integer'],
            [['same_day_cost'], 'number'],
            [['created_at', 'updated_at', 'expire_at'], 'safe'],
            [['code', 'name', 'material'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'includes_parts' => Yii::t('app', 'Includes Parts'),
            'same_day_service' => Yii::t('app', 'Same Day Service'),
            'same_day_cost' => Yii::t('app', 'Same Day Cost'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'expire_at' => Yii::t('app', 'Expire At'),
            'material' => Yii::t('app', 'Material'),
            'temporary_in' => Yii::t('app', 'Temporary In'),
            'temporary_out' => Yii::t('app', 'Temporary Out'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipments()
    {
        return $this->hasMany(Equipment::className(), ['contract_id' => 'id']);
    }
}
