<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "maintenance_log".
 *
 * @property integer $id
 * @property integer $maintenance_id
 * @property string $user_name
 * @property string $log_message
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Maintenance $maintenance
 *
 * @property string $status_label
 * @property label $status_list
 */
class MaintenanceLog extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'maintenance_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['maintenance_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_name', 'log_message'], 'string', 'max' => 255],
            [['maintenance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Maintenance::className(), 'targetAttribute' => ['maintenance_id' => 'id']],
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            //            'timestamp' => [
            //                'class'              => \yii\behaviors\TimestampBehavior::className(),
            //                'createdAtAttribute' => 'created_at',
            //                'updatedAtAttribute' => 'updated_at',
            //                'value'              => new \yii\db\Expression("now()"),
            //            ],
            'blameable' => [
                'class'              => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'    => [
                'class'     => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'status',
                'options'   => [
                    self::STATUS_ENABLED  => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                ]
            ],
            //    'multilingual' => [
            //        'class' => \yeesoft\multilingual\behaviors\MultilingualBehavior::className(),
            //        'attributes' => []
            //    ],
        ];
    }
    //    use \yeesoft\multilingual\db\MultilingualLabelsTrait;
    //    public static function find()
    //    {
    //        return new \yeesoft\multilingual\db\MultilingualQuery(get_called_class());
    //    }

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
            'id'             => 'ID',
            'maintenance_id' => 'Maintenance ID',
            'user_name'      => 'User Name',
            'log_message'    => 'Log Message',
            'status'         => 'Status',
            'created_at'     => 'Created At',
            'updated_at'     => 'Updated At',
            'created_by'     => 'Created By',
            'updated_by'     => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaintenance()
    {
        return $this->hasOne(Maintenance::className(), ['id' => 'maintenance_id']);
    }


    public static function log($id, $message, $username = null, $datetime = null)
    {
        if (empty($datetime)) {
            $datetime = gmdate("Y-m-d H:i:s");
        }
        if (empty($username)) {
            if (!empty(Yii::$app->user)) {
                if (!Yii::$app->user->isGuest) {
                    $user = Yii::$app->user->identity;
                    $username = $user->name;
                }
            } else {
                $username = "System";
            }
        }
        $maintenanceLog = new MaintenanceLog([
            'maintenance_id' => $id,
            'log_message'    => $message,
            'user_name'      => $username,
            'created_at'     => $datetime
        ]);
        $maintenanceLog->save();
        //        if (!empty($datetime)) {
        //            $maintenanceLog->created_at = $datetime;
        //            $maintenanceLog->save();
        //        }
    }
}
