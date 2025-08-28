<?php


class SubscriptionIndexCest
{
    public function nPlusOneIsFixed(FunctionalTester $I)
    {
        $I->wantTo('verify query count remains stable with eager loading');
        $this->markTestIncomplete('Implement using Yii profiler to assert constant number of queries.');
    }
}
