<?php

namespace api\controllers;

use Yii;
use yii\base\Exception;
use yii\filters\auth\HttpHeaderAuth;
use yii\rest\Controller;

/**
 * Site controller
 */
class SiteController extends Controller {

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

  public function actionTest($id = null) {
    //if ($id == null) {
      return ['response' => \Yii::t("app", 'Copyright')];
    //}
    throw new \yii\web\BadRequestHttpException("Error in: {$id}");
  }

  public function actionError() {
    $exception = Yii::$app->getErrorHandler()->exception;
    return [
        'name' => $exception->getName(),
        'message' => $exception->getMessage(),
        'code' => $exception->statusCode,
        'status' => $exception->statusCode,
        'type' => get_class($exception),
    ];
  }

}
