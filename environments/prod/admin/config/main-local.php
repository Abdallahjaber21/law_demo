<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
    ],
];


if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class'            => 'yii\debug\Module',
        'historySize'      => 500000,
        'allowedIPs'       => ['*'],
        'as accessControl' => [
            'class' => \yii\filters\AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['developer'],
                ],
            ],
        ],
    ];
    $config['modules']['api-debug'] = [
        'class'            => common\components\extensions\CrossAppDebug::class,
        'allowedIPs'       => ['*'],
        'historySize'      => 500000,
        'dataPath'         => Yii::getAlias("@api/runtime/debug"),
        'as accessControl' => [
            'class' => \yii\filters\AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['developer'],
                ],
            ],
        ],
    ];
    $config['modules']['technician-debug'] = [
        'class'            => common\components\extensions\CrossAppDebug::class,
        'allowedIPs'       => ['*'],
        'historySize'      =>  500000,
        'dataPath'         => Yii::getAlias("@technician/runtime/debug"),
        'as accessControl' => [
            'class' => \yii\filters\AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['developer'],
                ],
            ],
        ],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'      => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],
        'generators' => [ //here
            'crud'  => [ // generator name
                'class'     => 'console\giitemplates\crud\Generator', // generator class
                'templates' => [ //setting for out templates
                    'myCrud' => '@console/giitemplates/crud/default', // template name => path to template
                ]
            ],
            'model' => [ // generator name
                'class'     => 'yii\gii\generators\model\Generator', // generator class
                'templates' => [ //setting for out templates
                    'myCrud' => '@console/giitemplates/model/default', // template name => path to template
                ]
            ],
        ],
    ];
}

return $config;
