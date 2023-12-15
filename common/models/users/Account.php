<?php

namespace common\models\users;

use common\models\Technician;
use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "account".
 *
 * @property integer $id
 * @property string $type
 *
 * @author Tarek K. Ajaj
 */
class Account extends ActiveRecord
{

  /**
   * @inheritdoc
   */
  public static function tableName()
  {
    return 'account';
  }

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      [['type'], 'string'],
    ];
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'type' => Yii::t('app', 'Type'),
    ];
  }

  /**
   * 
   * @return AbstractAccount
   */
  public function getUserObject()
  {
    switch ($this->type) {
      case 'admin':
        return Admin::findOne(['account_id' => $this->id]);
      case 'user':
        return User::findOne(['account_id' => $this->id]);
      case 'technician':
        return Technician::findOne(['account_id' => $this->id]);
    }
    return FALSE;
  }
}
