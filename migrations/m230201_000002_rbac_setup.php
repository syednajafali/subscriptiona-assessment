<?php
use yii\db\Migration;

/**
 * Seeds RBAC roles/permissions idempotently.
 */
class m230201_000002_rbac_setup extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Permissions
        $viewAny = $auth->getPermission('subscription.viewAny') ?: $auth->createPermission('subscription.viewAny');
        $viewAny->description = 'View any user\'s subscriptions';
        if ($viewAny->isNewRecord ?? true) { $auth->add($viewAny); }

        $cancel = $auth->getPermission('subscription.cancel') ?: $auth->createPermission('subscription.cancel');
        $cancel->description = 'Cancel any subscription';
        if ($cancel->isNewRecord ?? true) { $auth->add($cancel); }

        // Roles
        $admin = $auth->getRole('admin') ?: $auth->createRole('admin');
        if ($admin->isNewRecord ?? true) { $auth->add($admin); }

        // Assign permissions to admin
        if (!$auth->hasChild($admin, $viewAny)) { $auth->addChild($admin, $viewAny); }
        if (!$auth->hasChild($admin, $cancel)) { $auth->addChild($admin, $cancel); }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        foreach (['subscription.viewAny','subscription.cancel'] as $name) {
            $perm = $auth->getPermission($name);
            if ($perm) { $auth->remove($perm); }
        }
    }
}
