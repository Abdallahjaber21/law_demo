<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;

/**
 * This is the model class for table "login_audit".
 *
 * @property int $id
 * @property string $ip_address
 * @property string $login_credential
 * @property int $login_status
 * @property string $datetime
 * @property string $logout
 * @property int $user_id
 *
 * @property Admin $user
 */
class LoginAudit extends \yii\db\ActiveRecord
{

    const LOGIN_SUCCESS = 10;
    const LOGIN_DENIED = 20;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'login_audit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login_status', 'user_id'], 'integer'],
            [['datetime', 'logout'], 'safe'],
            [['ip_address', 'login_credential'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip_address' => 'Ip Address',
            'login_credential' => 'Login Credential',
            'login_status' => 'Login Status',
            'login_status_label' => 'Login Status',
            'datetime' => 'Datetime',
            'logout' => 'Logout',
            'user_id' => 'User ID',
        ];
    }

    public function behaviors()
    {
        return [
            'login_status'    => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'login_status',
                'options'   => [
                    self::LOGIN_SUCCESS  => Yii::t("app", "Success"),
                    self::LOGIN_DENIED => Yii::t("app", "Failed"),
                ]
            ],
            //    'multilingual' => [
            //        'class' => \yeesoft\multilingual\behaviors\MultilingualBehavior::className(),
            //        'attributes' => []
            //    ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Admin::className(), ['id' => 'user_id']);
    }

    public static function logIp($type = null, $user_id = null, $is_new_record = false, $login_credential = null, $ip_address = null)
    {
        if ($is_new_record) {
            if (!empty($type)) {
                $ip_model = new LoginAudit();

                $ip_model->ip_address = Yii::$app->request->userIP;
                $ip_model->login_credential = @$login_credential;
                $ip_model->login_status = $type;
                $ip_model->datetime = gmdate("Y-m-d H:i:s");

                if ($type == LoginAudit::LOGIN_SUCCESS) {
                    $ip_model->user_id = @$user_id;
                }
                $ip_model->save();
            }
        } else { // logout
            $ip_model = LoginAudit::find()->where(['user_id' => $user_id, 'logout' => null])->orderBy(['datetime' => SORT_DESC])->one();

            if (!empty($ip_model)) {
                $ip_model->logout = gmdate("Y-m-d H:i:s");
                $ip_model->save();
            }
        }
    }
}
