<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "worker".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property string $phone_number
 * @property string $image
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $random_token
 *
 * @property WorkerSector[] $workerSectors
 * @property Sector[] $sectors
 *
 * @property string $image_url
 * @property string $image_path
 * @property string $image_thumb_url
 * @property string $image_thumb_path
 * @property string $status_label
 * @property label $status_list
 */
class Worker extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'title', 'phone_number', 'random_token'], 'string', 'max' => 255],
            ['image', 'file', 'extensions' => 'jpg,png,jpeg', 'maxSize' => (1024 * 1024) * 2],
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'random_token' => [
                'class'      => \common\behaviors\RandomTokenBehavior::className(),
                'attributes' => ['random_token'],
            ],
            'timestamp'    => [
                'class'              => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new \yii\db\Expression("now()"),
            ],
            'blameable'    => [
                'class'              => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'       => [
                'class'     => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'status',
                'options'   => [
                    self::STATUS_ENABLED  => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                ]
            ],
            'image'        => [
                'class'     => \common\behaviors\ImageUploadBehavior::className(),
                'attribute' => 'image',
                'createThumbsOnRequest' => true,
                'thumbs'    => [
                    'thumb' => ['width' => 250, 'height' => 250],
                    'thumb_100' => ['width' => 100, 'height' => 100],
                ],
                'filePath'  => '@static/upload/images/worker/worker_[[pk]]_[[attribute_random_token]].[[extension]]',
                'fileUrl'   => '@staticWeb/upload/images/worker/worker_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
                'thumbPath' => '@static/upload/images/worker/[[profile]]/worker_[[pk]]_[[attribute_random_token]].[[extension]]',
                'thumbUrl'  => '@staticWeb/upload/images/worker/[[profile]]/worker_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
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
            'id'           => 'ID',
            'name'         => 'Name',
            'title'        => 'Title',
            'phone_number' => 'Phone Number',
            'image'        => 'Image',
            'status'       => 'Status',
            'created_at'   => 'Created At',
            'updated_at'   => 'Updated At',
            'created_by'   => 'Created By',
            'updated_by'   => 'Updated By',
            'random_token' => 'Random Token',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkerSectors()
    {
        return $this->hasMany(WorkerSector::className(), ['worker_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSectors()
    {
        return $this->hasMany(Sector::className(), ['id' => 'sector_id'])->viaTable('worker_sector', ['worker_id' => 'id']);
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'title',
            'phone_number',
            'image_thumb_100_url',
        ];
    }
}
