<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => "{$params['project-id']}-admin",
    'name' => "{$params['project-name']}",
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'admin\controllers',
    'bootstrap' => [
        'log',
        common\components\extensions\MultilingualBootstrapInterface::class,
        //'translatemanager' //Uncomment to activate front end translations       
    ],
    'controllerMap' => [
        'settings' => \common\components\settings\SettingsController::class,
        'notification' => 'common\components\notification\NotificationController',
    ],
    //    'on beforeRequest' => function () {
    //      $user = Yii::$app->user->identity;
    //      if (!empty($user) &&
    //              !empty($user->timezone)) {
    //        Yii::$app->setTimeZone($user->timezone);
    //          Yii::$app->getFormatter()->timeZone = $user->timezone;
    //      }
    //    },
    'on beforeRequest' => function () {
        $user = Yii::$app->user->identity;
        if (!empty($user) && !empty($user->timezone)) {
            Yii::$app->setTimeZone($user->timezone);
            Yii::$app->getFormatter()->timeZone = $user->timezone;
        }
    },
    'modules' => [
        'rbac' => [
            'class' => 'common\components\rbac\RbacModule',
        ],
        'translatemanager' => [
            'class' => 'lajax\translatemanager\Module',
            'layout' => null,
            'allowedIPs' => ['*'],
            'roles' => ['@', 'manage-translation'],
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-admin',
        ],
        'user' => [
            'identityClass' => 'common\models\Admin',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-admin', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the admin
            'name' => "{$params['project-id']}-admin",
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'assetManager' => [],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => $params['urlManagers']['admin'],
        //Uncomment to activate front end translations            
        //'translatemanager' => [
        //    'class' => 'lajax\translatemanager\Component'
        //]
    ],
    'params' => $params,
];
