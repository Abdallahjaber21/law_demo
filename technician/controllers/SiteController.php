<?php

namespace technician\controllers;

use Yii;
use yii\rest\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function actionTest($id = null)
    {
        return ['response' => \Yii::t("app", 'Copyright')];
    }

    public function actionError()
    {
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
