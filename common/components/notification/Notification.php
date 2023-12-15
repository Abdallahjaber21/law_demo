<?php

namespace common\components\notification;

use common\behaviors\DateFormatBehavior;
use common\behaviors\OptionsBehavior;
use common\behaviors\RelativeTimeBehavior;
use common\models\Customer;
use common\models\UserLocation;
use common\models\users\Account;
use common\models\users\Admin;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property integer $account_id
 * @property integer $type
 * @property string $title
 * @property string $message
 * @property string $params
 * @property string $url
 * @property string $data
 * @property string $mobile_action
 * @property integer $seen
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $relation_key
 *
 * @property Account $account
 * @property NotificationLang[] $notificationLangs
 *
 * @property string $type_label
 * @property array $type_list
 * @property string $status_label
 * @property label $status_list
 */
class Notification extends ActiveRecord
{

    // Status
    const STATUS_ENABLED = 10;
    const STATUS_DISABLED = 20;
    // Status
    const TYPE_NOTIFICATION = 10;
    const TYPE_WARNING = 20;

    const TYPE_SERVICE = 30;
    const TYPE_CONTRACT = 40;
    const TYPE_NEWS = 50;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    public static function findEnabled()
    {
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }

    public static function markRead($account_id, $id)
    {
        Notification::updateAll(['seen' => true, 'status' => self::STATUS_DISABLED], [
            'AND',
            ['id' => $id],
            ['account_id' => $account_id]
        ]);
    }

    public static function notifyAdmins($message, $title = "", $params = [], $url = ["/site/index"], $data = null, $type = 10, $relation_key = null)
    {
        $users = Admin::find()
            ->select(['account_id'])
            ->where([
                'AND',
                ['status' => Admin::STATUS_ENABLED],
                [
                    'OR',
                    ['enable_notification' => true],
                    ['enable_notification' => 1],
                ],
                [
                    'OR',
                    ['IS NOT', 'mobile_registration_id', Null],
                    ['IS NOT', 'web_registration_id', Null],
                ]
            ])
            ->asArray()
            ->all();

        Yii::info("Notifying " . count($users), 'NOTIFICATION');
        foreach ($users as $user) {
            Yii::info("Notifying {$user['account_id']}", 'NOTIFICATION');
            static::createNotification($user['account_id'], $message, $title, $params, $url, $data, $type, $relation_key);
        }
    }

    public static function createNotification($account_id, $message, $title = "", $params = [], $url = ["/site/index"], $mobile_action = ['action' => 'open'], $data = null, $type = 10, $relation_key = null)
    {
        $notification = new Notification();
        $notification->account_id = $account_id;
        $notification->title = !empty($title) ? $title : \Yii::$app->params['project-name'];
        $notification->message = $message;
        $notification->params = is_array($params) ? Json::encode($params) : [];
        $notification->url = Json::encode($url);
        $notification->mobile_action = Json::encode($mobile_action);
        $notification->data = !empty($data) ? Json::encode($data) : null;
        $notification->type = $type;
        $notification->seen = false;
        $notification->relation_key = $relation_key;
        if ($notification->save()) {
            $id = Yii::$app->queue->push(new NotificationJob([
                'notification_id' => $notification->id,
            ]));
        }
    }

    public static function notifyCustomerUsers($customer_id, $message, $title = "", $params = [], $url = ["/site/index"], $mobile_action = ['action' => 'open'], $data = null, $type = 10, $relation_key = null)
    {
        $customer = Customer::findOne($customer_id);
        $users = $customer->getUsers()->select(['account_id'])->asArray()->all();
        foreach ($users as $user) {
            Notification::deleteAll(['AND', ['relation_key' => $relation_key], ['account_id' => $user['account_id']]]);
            static::createNotification($user['account_id'], $message, $title, $params, $url, $mobile_action, $data, $type, $relation_key);
        }
    }

    public static function notifyLocationUsers($location_id, $message, $title = "", $params = [], $url = ["/site/index"], $mobile_action = ['action' => 'open'], $data = null, $type = 10, $relation_key = null)
    {
        $users = UserLocation::find()
            ->select(['user_id'])
            ->where(['location_id' => $location_id])
            ->asArray()
            ->all();
        foreach ($users as $user) {
            Notification::deleteAll(['AND', ['relation_key' => $relation_key], ['account_id' => $user['user_id']]]);
            static::createNotification($user['user_id'], $message, $title, $params, $url, $mobile_action, $data, $type, $relation_key);
        }
    }

    public static function notifyEquipmentUsers($equipment, $message, $title = "", $params = [], $url = ["/site/index"], $mobile_action = ['action' => 'open'], $data = null, $type = 10, $relation_key = null)
    {
        $users = UserLocation::find()
            ->select(['user_id'])
            ->where(['location_id' => $equipment->location_id])
            ->andWhere([
                'OR',
                ['<=', "FIND_IN_SET({$equipment->id}, removed_units)", 0],
                ['IS', "FIND_IN_SET({$equipment->id}, removed_units)", null],
            ])
            ->asArray()
            ->all();
        foreach ($users as $user) {
            Notification::deleteAll(['AND', ['relation_key' => $relation_key], ['account_id' => $user['user_id']]]);
            static::createNotification($user['user_id'], $message, $title, $params, $url, $mobile_action, $data, $type, $relation_key);
        }
    }

    public static function notifyTechnician($technician_id, $message, $title = "", $params = [], $url = ["/site/index"], $mobile_action = ['action' => 'open'], $data = null, $type = 10, $relation_key = null)
    {
        static::createNotification($technician_id, $message, $title, $params, $url, $mobile_action, $data, $type, $relation_key);
    }

    public static function notifyUsers($usersIds, $message, $title = "", $params = [], $url = ["/site/index"], $mobile_action = ['action' => 'open'], $data = null, $type = 10, $relation_key = null)
    {
        if (is_array($usersIds)) {
            foreach ($usersIds as $user_id) {
                static::createNotification($user_id, $message, $title, $params, $url, $mobile_action, $data, $type, $relation_key);
            }
        } else {
            static::createNotification($usersIds, $message, $title, $params, $url, $mobile_action, $data, $type, $relation_key);
        }
    }

    public static function notifyByEmail($to, $config = [])
    {
        Yii::$app->mailer->compose($config['view'], ['data' => $config])
            ->setFrom(\Yii::$app->params['notificationEmail'])
            ->setTo($to)
            ->setSubject(\Yii::$app->params['project-name'] . ' - ' . $config['title'])
            ->send();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id'], 'required'],
            [['account_id', 'type', 'status', 'created_by', 'updated_by', 'relation_key'], 'integer'],
            [['data'], 'string'],
            [['seen'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['url', 'message'], 'string', 'max' => 255],
            [['params'], 'string'],
            [['title'], 'string'],
            [['mobile_action'], 'string'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp'            => [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression("now()"),
            ],
            'blameable'            => [
                'class'              => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'               => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'status',
                'options'   => [
                    self::STATUS_ENABLED  => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                ]
            ],
            'type'                 => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'type',
                'options'   => [
                    self::TYPE_NOTIFICATION => Yii::t("app", "Notification"),
                    self::TYPE_WARNING      => Yii::t("app", "Warning"),
                    self::TYPE_SERVICE      => Yii::t("app", "Service"),
                    self::TYPE_SERVICE      => Yii::t("app", "Service"),
                    self::TYPE_CONTRACT     => Yii::t("app", "Contract"),
                    self::TYPE_NEWS         => Yii::t("app", "News"),
                ]
            ],
            'created_at_formatted' => [
                'class'      => DateFormatBehavior::className(),
                'attributes' => ['created_at']
            ],
            'created_at_relative'  => [
                'class'     => RelativeTimeBehavior::className(),
                'attribute' => 'created_at'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('app', 'ID'),
            'account_id'    => Yii::t('app', 'Account ID'),
            'type'          => Yii::t('app', 'Type'),
            'message'       => Yii::t('app', 'Message'),
            'title'         => Yii::t('app', 'Title'),
            'params'        => Yii::t('app', 'Params'),
            'url'           => Yii::t('app', 'Url'),
            'data'          => Yii::t('app', 'Data'),
            'seen'          => Yii::t('app', 'Seen'),
            'status'        => Yii::t('app', 'Status'),
            'created_at'    => Yii::t('app', 'Created At'),
            'updated_at'    => Yii::t('app', 'Updated At'),
            'created_by'    => Yii::t('app', 'Created By'),
            'updated_by'    => Yii::t('app', 'Updated By'),
            'mobile_action' => Yii::t('app', 'Mobile action'),
            'relation_key'  => Yii::t('app', 'Relation Key'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    public function fields()
    {
        return [
            'id',
            'seen'          => function ($model) {
                return boolval($model->seen);
            },
            'title'         => function ($model) {
                return NotificationMessages::getMessage($model->title, Json::decode($model->params));
            },
            'message'       => function ($model) {
                return NotificationMessages::getMessage($model->message, Json::decode($model->params));
            },
            'mobile_action' => function ($model) {
                return Json::decode($model->mobile_action);
            },
            'created_at_relative',
            'created_at',
        ];
    }
}
