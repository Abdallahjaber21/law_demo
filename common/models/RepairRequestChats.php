<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "repair_request_chats".
 *
 * @property integer $id
 * @property integer $request_id
 * @property integer $assignee_id
 * @property integer $gallery_id
 * @property string $message
 * @property string $audio
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Assignee $assignee
 * @property Gallery $gallery
 * @property RepairRequest $request
 *
 * @property string $status_label
 * @property label $status_list
 */
class RepairRequestChats extends \yii\db\ActiveRecord
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
        return 'repair_request_chats';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request_id', 'assignee_id', 'gallery_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['message', 'audio'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['assignee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Assignee::className(), 'targetAttribute' => ['assignee_id' => 'user_id']],
            [['gallery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gallery::className(), 'targetAttribute' => ['gallery_id' => 'id']],
            [['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => RepairRequest::className(), 'targetAttribute' => ['request_id' => 'id']],
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
            'request_id' => 'Request ID',
            'assignee_id' => 'Assignee ID',
            'gallery_id' => 'Gallery ID',
            'message' => 'Message',
            'audio' => 'Audio',
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
    public function getAssignee()
    {
        return $this->hasOne(Assignee::className(), ['user_id' => 'assignee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasOne(Gallery::className(), ['id' => 'gallery_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequest()
    {
        return $this->hasOne(RepairRequest::className(), ['id' => 'request_id']);
    }

    public function fields()
    {
        return [
            'id',
            'assignee_id',
            'created_at' => function ($model) {
                // Yii::$app->formatter->timeZone = Yii::$app->user->identity->timezone;
                // date_default_timezone_set(Yii::$app->user->identity->timezone);
                // return Yii::$app->formatter->asRelativeTime(
                return $model->created_at;
            },
            'message',
            'name' => function ($model) {
                return $model->assignee->user->name;
            },
            'avatar' => function ($model) {
                return $model->assignee->user->image_thumb_url;
            },
            'image' => function ($model) {
                if ($model->gallery_id) {
                    return $model->gallery->images;
                }
            },
            'audio' => function ($model) {
                return $model->getAudioUrl($model->audio);
            },
        ];
    }
    public function getAudioUrl($audio_name)
    {

        if (!empty($audio_name)) {
            return Yii::getAlias('@staticWeb/upload/audio/') . $audio_name;
        }

        return null;
    }
}