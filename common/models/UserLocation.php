<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "user_location".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $location_id
 * @property integer $role
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $removed_units
 * @property boolean $is_locked
 *
 * @property Location $location
 * @property User $user
 *
 * @property string $status_label
 * @property label $status_list
 */
class UserLocation extends ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;

    // Status
    const ROLE_RESIDENT = 10;
    const ROLE_DECISION_MAKER = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_location';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'location_id', 'role', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['role'], 'default', 'value' => self::ROLE_RESIDENT],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['removed_units'], 'string'],
            [['is_locked'], 'boolean'],
            [['is_locked'], 'default', 'value' => false],
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression("now()"),
            ],
            'blameable' => [
                'class'              => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'    => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'status',
                'options'   => [
                    self::STATUS_ENABLED  => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                ]
            ],
            'role'      => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'role',
                'options'   => [
                    self::ROLE_DECISION_MAKER => Yii::t("app", "Decision Maker"),
                    self::ROLE_RESIDENT       => Yii::t("app", "Resident"),
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'user_id'     => 'User ID',
            'location_id' => 'Location ID',
            'role'        => 'Role',
            'status'      => 'Status',
            'created_at'  => 'Created At',
            'updated_at'  => 'Updated At',
            'created_by'  => 'Created By',
            'updated_by'  => 'Updated By',
            'is_locked'  => 'Lock user from activating units?',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function fields()
    {
        return [
            'id',
            'user' => function (UserLocation $model) {
                return "{$model->user->firstname} {$model->user->lastname}";
            },
            'is_locked',
            'role',
            'role_label',
        ];
    }
}
