<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "location_contract".
 *
 * @property int $id
 * @property int $location_id
 * @property string $description
 * @property string $expiry_date
 * @property int $block_service
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Location $location
 */
class LocationContract extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'location_contract';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['location_id', 'block_service', 'status', 'created_by', 'updated_by'], 'integer'],
            [['description'], 'string'],
            [['expiry_date', 'created_at', 'updated_at'], 'safe'],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'location_id' => Yii::t('app', 'Location ID'),
            'description' => Yii::t('app', 'Description'),
            'expiry_date' => Yii::t('app', 'Expiry Date'),
            'block_service' => Yii::t('app', 'Block Service'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }
}
