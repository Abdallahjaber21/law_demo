<?php

namespace common\models;

use common\behaviors\ImageUploadBehavior;
use common\behaviors\OptionsBehavior;
use common\behaviors\RandomTokenBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "image".
 *
 * @property integer $id
 * @property integer $gallery_id
 * @property string $image
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $random_token
 * @property string $note
 *
 * @property Gallery $gallery
 *
 * @property string $image_url
 * @property string $image_path
 * @property string $image_thumb_url
 * @property string $image_thumb_path
 * @property string $status_label
 * @property label $status_list
 */
class Image extends ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'image';
    }

    public static function findEnabled()
    {
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gallery_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'note'], 'safe'],
            [['random_token'], 'string', 'max' => 255],
            [['gallery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gallery::className(), 'targetAttribute' => ['gallery_id' => 'id']],
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
                'class' => RandomTokenBehavior::className(),
                'attributes' => ['random_token'],
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression("now()"),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
                    self::STATUS_ENABLED => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                ]
            ],
            'image' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'image',
                'thumbs' => [
                    'thumb' => ['width' => 250, 'height' => 250],
                ],
                'filePath' => '@static/upload/images/gallery/[[attribute_gallery_id]]/image_[[pk]]_[[attribute_random_token]].[[extension]]',
                'fileUrl' => '@staticWeb/upload/images/gallery/[[attribute_gallery_id]]/image_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
                'thumbPath' => '@static/upload/images/gallery/[[attribute_gallery_id]]/[[profile]]/image_[[pk]]_[[attribute_random_token]].[[extension]]',
                'thumbUrl' => '@staticWeb/upload/images/gallery/[[attribute_gallery_id]]/[[profile]]/image_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'gallery_id' => Yii::t('app', 'Gallery ID'),
            'image' => Yii::t('app', 'Image'),
            'note' => Yii::t('app', 'Note'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'random_token' => Yii::t('app', 'Random Token'),
        ];
    }


    /**
     * @return ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasOne(Gallery::className(), ['id' => 'gallery_id']);
    }

    public function fields()
    {
        return [
            'image_url',
            'note'
        ];
    }
}
