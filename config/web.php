<?php
return [
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'user' => [
            'identityClass' => 'app\modules\subscription\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db',
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'mutex' => \yii\mutex\MysqlMutex::class,
        ],
    ],
    'modules' => [
        'subscription' => [
            'class' => 'app\modules\subscription\Module',
        ],
    ],
];
