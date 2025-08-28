<?php
namespace app\modules\subscription\services;

use Yii;
use app\modules\subscription\models\Subscription;
use app\jobs\SendSubscriptionEmailJob;
use yii\base\BaseObject;
use yii\db\Expression;

class SubscriptionService extends BaseObject
{

    public function convertExpiredTrialsToPaid(): int
    {
        $now = date('Y-m-d H:i:s');

        $trials = Subscription::find()
            ->where(['type' => 'trial', 'status' => 'active'])
            ->andWhere(['<=', 'trial_end_at', $now])
            ->all();

        $count = 0;
        foreach ($trials as $sub) {
            $sub->type = 'paid';
            $sub->updated_at = $now;
            if ($sub->save(false, ['type','updated_at'])) {
                $count++;
                Yii::$app->queue->push(new SendSubscriptionEmailJob([
                    'userId' => $sub->user_id,
                    'subscriptionId' => $sub->id,
                    'subject' => 'Your trial has been converted to a paid subscription',
                    'body' => 'Thank you for continuing with our service.',
                ]));
            }
        }
        return $count;
    }

    public function cancel(Subscription $sub): bool
    {
        $sub->status = 'cancelled';
        $sub->updated_at = date('Y-m-d H:i:s');
        return $sub->save(false, ['status','updated_at']);
    }
}
