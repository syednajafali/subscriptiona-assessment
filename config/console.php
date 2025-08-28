<?php
return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',

    'controllerMap' => [
        'trial' => [
            'class' => 'app\console\controllers\TrialController',
        ],
    ],
    'bootstrap' => ['queue'],
    'components' => [
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db',
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'mutex' => \yii\mutex\MysqlMutex::class,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'db' => require __DIR__ . '/db.php',
    ],
];
