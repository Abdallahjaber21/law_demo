<?php

return [
    'admin' => [
        'baseUrl' => Yii::getAlias("@adminWeb"),
        'class' => \common\components\extensions\MultilingualUrlManager::className(),
        //'class' => yii\web\UrlManager::class,
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [
            '<language:([a-zA-Z-]{2,5})?>' => 'site/index',
            '<language:([a-zA-Z-]{2,5})?>/<action:[\w \-]+>' => 'site/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<controller:[\w \-]+>/<action:[\w \-]+>/<id:\d+>' => '<controller>/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<controller:[\w \-]+>/<action:[\w \-]+>' => '<controller>/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<module:[\w \-]+>/<controller:[\w \-]+>/<action:[\w \-]+>' => '<module>/<controller>/<action>',
        ],
    ],
    'api' => [
        'baseUrl' => Yii::getAlias("@apiWeb"),
        'class' => \common\components\extensions\MultilingualUrlManager::className(),
        //'class' => yii\web\UrlManager::class,
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [
            //"OPTIONS <url:.+>" => "v1/site/test",//Fix CORS faile for option due to authorization
            '<language:([a-zA-Z-]{2,5})?>' => 'site/index',
            '<language:([a-zA-Z-]{2,5})?>/<action:[\w \-]+>' => 'site/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<controller:[\w \-]+>/<action:[\w \-]+>/<id:\d+>' => '<controller>/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<controller:[\w \-]+>/<action:[\w \-]+>' => '<controller>/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<module:[\w \-]+>/<controller:[\w \-]+>/<action:[\w \-]+>/<id:\d+>' => '<module>/<controller>/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<module:[\w \-]+>/<controller:[\w \-]+>/<action:[\w \-]+>/' => '<module>/<controller>/<action>',
        ],
    ],
    'technician' => [
        'baseUrl' => Yii::getAlias("@technicianWeb"),
        'class' => \common\components\extensions\MultilingualUrlManager::className(),
        //'class' => yii\web\UrlManager::class,
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [
            //"OPTIONS <url:.+>" => "v1/site/test",//Fix CORS faile for option due to authorization
            '<language:([a-zA-Z-]{2,5})?>' => 'site/index',
            '<language:([a-zA-Z-]{2,5})?>/<action:[\w \-]+>' => 'site/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<controller:[\w \-]+>/<action:[\w \-]+>/<id:\d+>' => '<controller>/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<controller:[\w \-]+>/<action:[\w \-]+>' => '<controller>/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<module:[\w \-]+>/<controller:[\w \-]+>/<action:[\w \-]+>/<id:\d+>' => '<module>/<controller>/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<module:[\w \-]+>/<controller:[\w \-]+>/<action:[\w \-]+>' => '<module>/<controller>/<action>',
        ],
    ],
    'common' => [
        'baseUrl' => Yii::getAlias("@common"),
        'class' => \common\components\extensions\MultilingualUrlManager::className(),
        //'class' => yii\web\UrlManager::class,
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [
            //"OPTIONS <url:.+>" => "v1/site/test",//Fix CORS faile for option due to authorization
            '<language:([a-zA-Z-]{2,5})?>' => 'site/index',
            '<language:([a-zA-Z-]{2,5})?>/<action:[\w \-]+>' => 'site/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<controller:[\w \-]+>/<action:[\w \-]+>/<id:\d+>' => '<controller>/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<controller:[\w \-]+>/<action:[\w \-]+>' => '<controller>/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<module:[\w \-]+>/<controller:[\w \-]+>/<action:[\w \-]+>/<id:\d+>' => '<module>/<controller>/<action>',
            '<language:([a-zA-Z-]{2,5})?>/<module:[\w \-]+>/<controller:[\w \-]+>/<action:[\w \-]+>' => '<module>/<controller>/<action>',
        ],
    ],
];
