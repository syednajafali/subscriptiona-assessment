<?php
namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SendSubscriptionEmailJob extends BaseObject implements JobInterface
{
    public $userId;
    public $subscriptionId;
    public $subject;
    public $body;

    public function execute($queue)
    {
        Yii::info("Email to user {$this->userId} about subscription {$this->subscriptionId}: {$this->subject}", __METHOD__);
    }
}
