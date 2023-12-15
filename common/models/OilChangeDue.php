<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oil_change_due".
 *
 * @property integer $id
 * @property integer $repair_request_id
 * @property integer $asset_id
 * @property double $next_oil_change
 * @property string $datetime
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property LocationEquipments $asset
 * @property RepairRequest $repairRequest
 *
 * @property string $status_label
 * @property label $status_list
 */
class OilChangeDue extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    //const STATUS_DELETED = 30;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oil_change_due';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['repair_request_id', 'asset_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['next_oil_change'], 'number'],
            [['datetime', 'created_at', 'updated_at'], 'safe'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => LocationEquipments::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [['repair_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => RepairRequest::className(), 'targetAttribute' => ['repair_request_id' => 'id']],
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression("now()"),
            ],
            'blameable' => [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status' => [
                'class' => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
                    self::STATUS_ENABLED => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                    //self::STATUS_DELETED => Yii::t("app", "Deleted"),
                ]
            ],
            // 'multilingual' => [
// 'class' => \yeesoft\multilingual\behaviors\MultilingualBehavior::className(),
// 'attributes' => []
// ],
        ];
    }
    // use \yeesoft\multilingual\db\MultilingualLabelsTrait;
// public static function find()
// {
// return new \yeesoft\multilingual\db\MultilingualQuery(get_called_class());
// }

    public static function findEnabled()
    {
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'repair_request_id' => 'Repair Request ID',
            'asset_id' => 'Asset ID',
            'next_oil_change' => 'Next Oil Change',
            'datetime' => 'Datetime',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @inheritdoc
     */

    /*
    public function beforeDelete() {
    if (parent::beforeDelete()) {
    if ($this->status == self::STATUS_ENABLED) {
    $this->status = self::STATUS_DISABLED;
    $this->save();
    return false;
    } else {
    return true;
    }
    }
    return false;
    }
    */

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(LocationEquipments::className(), ['id' => 'asset_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequest()
    {
        return $this->hasOne(RepairRequest::className(), ['id' => 'repair_request_id']);
    }
}