<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
        'setting' => [
            'class' => common\components\settings\ConsoleSettingsController::class,
        ],
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationNamespaces' => [
                'console\migrations\namespaced',
                'lajax\translatemanager\migrations\namespaced',
            ],
        ],
        'translate' => \lajax\translatemanager\commands\TranslatemanagerController::className()
    ],
    'components' => [
        'log' => [
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logVars' => [],
                    'categories' => ['integrity'],
                    'exportInterval' => 1,
                    'logFile' => '@runtime/logs/integrity.log',

                ],
            ],
        ],
        'cacheadmin' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => Yii::getAlias('@admin') . '/runtime/cache'
        ],
    ],
    'params' => $params,
];
