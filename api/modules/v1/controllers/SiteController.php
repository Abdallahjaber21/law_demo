<?php

namespace api\modules\v1\controllers;

use common\models\User;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors'  => [
                'Origin'                        => [
                    'app://localhost',
                    'https://localhost',
                    'http://localhost:8080',
                    'http://localhost:8081',
                ],
                'Access-Control-Allow-Headers' => ["*"],
                'Access-Control-Expose-Headers' => [
                    'X-Pagination-Current-Page',
                    'X-Pagination-Page-Count',
                    'X-Pagination-Total-Count',
                    'X-Pagination-Per-Page',
                ],
            ]
        ];

        return $behaviors;
    }

    public function actionTest($id = null)
    {
//        if ($id == null) {
            return ['response' => \Yii::t("app", 'Copyright')];
//        }
        throw new ServerErrorHttpException("Error in: {$id}");
    }


    public function actionSql()
    {
        return User::find()->where(
            ['OR',
                ['LIKE', "name", \Yii::$app->getRequest()->post("name")],
                ['LIKE', "password", \Yii::$app->getRequest()->post("name")]
            ]
        )->one();
    }


    public function actionClearRegid()
    {
        $regid = \Yii::$app->request->post("regid");
        if(!empty($regid)) {
            User::updateAll(['mobile_registration_id' => null], ['mobile_registration_id' => $regid]);
        }
        return ['success'=>true];
    }

}
