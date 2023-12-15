<?php

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',

    // 'on beforeRequest' => function () {
    //     Yii::$app->setTimeZone("Asia/Dubai");
    //     Yii::$app->getFormatter()->timeZone = "Asia/Dubai";
    // },
    // 'on beforeRequest' => function () {
    //     $user = Yii::$app->user->identity;
    //     if (!empty($user) && !empty($user->timezone)) {
    //         Yii::$app->setTimeZone($user->timezone);
    //         Yii::$app->getFormatter()->timeZone = $user->timezone;
    //     }
    // },
    'components' => [
        'settings' => [
            'class' => common\components\settings\SettingComponent::className(),
            'settings' => require(__DIR__ . '/includes/_settings.php')
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'cache' => 'cache',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            //'defaultTimeZone' => 'Asia/Dubai', //global date formats for display for your locale.
        ],

        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'db' => 'db',
                    'sourceLanguage' => 'en-US', // Developer language
                    'sourceMessageTable' => '{{%language_source}}',
                    'messageTable' => '{{%language_translate}}',
                    'cachingDuration' => 86400,
                    'enableCaching' => YII_ENV_PROD,
                    'forceTranslation' => true
                ],
                'app*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'db' => 'db',
                    'sourceLanguage' => 'en-US', // Developer language
                    'sourceMessageTable' => '{{%language_source}}',
                    'messageTable' => '{{%language_translate}}',
                    'cachingDuration' => 86400,
                    'enableCaching' => YII_ENV_PROD,
                    'forceTranslation' => true
                ],
                'rbac*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'db' => 'db',
                    'sourceLanguage' => 'en-US', // Developer language
                    'sourceMessageTable' => '{{%language_source}}',
                    'messageTable' => '{{%language_translate}}',
                    'cachingDuration' => 86400,
                    'enableCaching' => YII_ENV_PROD,
                    'forceTranslation' => true
                ],
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'dosamigos\google\maps\MapAsset' => [
                    'options' => [
                        'key' => $params['googleMapsKey'],
                        //'language' => 'id',
                        //'version' => '3.1.18'
                    ]
                ]
            ]
        ],
        'urlManagerAdmin' => $params['urlManagers']['admin'],
        'urlManagerApi' => $params['urlManagers']['api'],
        'urlManagerCommon' => $params['urlManagers']['common'],
        'urlManagerTechnician' => $params['urlManagers']['technician'],
        'queue' => [
            'class' => \yii\queue\file\Queue::className(),
            'as log' => \yii\queue\LogBehavior::className(),
            'path' => '@console/runtime/queue',
        ],
        'bitly' => [
            'class' => \common\components\Bitly::className(),
            'domain' => 'bit.ly',
            'token' => '4fd5c5fb2ff11b6ed728e3b98433769641203af8',
            'group_guid' => 'Bl9a69RRxL8'
        ]
    ],
];
