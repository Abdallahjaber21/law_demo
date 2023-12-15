<?php

namespace common\components\extensions\api;

use Yii;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * API Base Controller
 * All controllers within API app must extend this controller!
 */

/**
 * Description of ApiController
 *
 * @author Tarek K. Ajaj
 */
class ApiController extends Controller
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);
        // add QueryParamAuth for authentication
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors'  => [
                'Origin'  => [
                    'app://localhost',
                    'https://localhost',
                    'http://localhost:8080',
                    'http://localhost:8081',
                    'http://localhost:8082',
                    'https://demo.e-maintain.com',
                ],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Allow-Headers' => ["*"],
                'Access-Control-Expose-Headers' => [
                    'X-Pagination-Current-Page',
                    'X-Pagination-Page-Count',
                    'X-Pagination-Total-Count',
                    'X-Pagination-Per-Page',
                ],
            ]
        ];

        $behaviors['authenticator'] = [
            'class' => HttpHeaderAuth::className(),
        ];

        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }

    public function beforeAction($action)
    {
        $before = parent::beforeAction($action);
        Yii::$app->setTimeZone("Asia/Dubai");
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            if (
                !empty($user) &&
                !empty($user->timezone)
            ) {
                Yii::$app->setTimeZone($user->timezone);
                Yii::$app->formatter->timeZone = $user->timezone;
            }
        }
        return $before;
    }

    /**
     * @return \yii\web\IdentityInterface
     * @throws \Throwable
     */
    public function getUser()
    {
        return Yii::$app->getUser()->getIdentity();
    }

    public function isPost()
    {
        if (!Yii::$app->getRequest()->isPost) {
            throw new NotFoundHttpException(\Yii::t("app", "Request method not supported"));
        }
    }

    public function isGet()
    {
        if (!Yii::$app->getRequest()->isGet) {
            throw new NotFoundHttpException(\Yii::t("app", "Request method not supported"));
        }
    }
}
