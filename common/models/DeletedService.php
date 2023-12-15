<?php

namespace common\models;

use Yii;

/**
* This is the model class for table "deleted_service".
*
* @property integer $id
* @property integer $service_id
* @property string $model
* @property string $logs
*
*/
class DeletedService extends \yii\db\ActiveRecord
{



    /**
    * @inheritdoc
    */
    public static function tableName()
    {
    return 'deleted_service';
    }

    /**
    * @inheritdoc
    */
    public function rules()
    {
    return [
            [['service_id'], 'integer'],
            [['model', 'logs'], 'string'],
        ];
    }


    /**
    * @inheritdoc
    */
    public function behaviors() {
        return [
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
    
    public static function findEnabled() {
        return parent::find()->where(['status'=> self::STATUS_ENABLED]);
    }
    
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_id' => 'Service ID',
            'model' => 'Model',
            'logs' => 'Logs',
            ];
    }
    
    /**
     * @inheritdoc
     */
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
}
