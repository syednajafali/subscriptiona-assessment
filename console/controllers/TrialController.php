<?php
namespace app\console\controllers;

use yii\console\Controller;
use app\modules\subscription\services\SubscriptionService;

class TrialController extends Controller
{
    /**
     * Convert expired trials to paid and queue email notifications.
     * Usage: php yii trial/convert
     */
    public function actionConvert()
    {
        $service = new SubscriptionService();
        $count = $service->convertExpiredTrialsToPaid();
        $this->stdout("Converted {$count} trial subscriptions.\n");
        return self::EXIT_CODE_NORMAL;
    }
}
