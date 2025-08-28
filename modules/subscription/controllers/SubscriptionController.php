<?php
namespace app\modules\subscription\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\modules\subscription\models\Subscription;
use app\modules\subscription\services\SubscriptionService;

class SubscriptionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index','view'],
                        'roles' => ['@'], 
                    ],
                    [
                        'allow' => true,
                        'actions' => ['cancel'],
                        'roles' => ['subscription.cancel'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = Subscription::find()->with(['user','plan']); // eager load -> fix N+1
        if (!Yii::$app->user->can('subscription.viewAny')) {
            $query->andWhere(['user_id' => Yii::$app->user->id]);
        }
        $subs = $query->all();
        return $this->render('index', ['subs' => $subs]);
    }

    public function actionView($id)
    {
        $sub = Subscription::find()->with(['user','plan'])->where(['id' => (int)$id])->one();
        if (!$sub) {
            throw new NotFoundHttpException('Subscription not found');
        }
        if ($sub->user_id != Yii::$app->user->id && !Yii::$app->user->can('subscription.viewAny')) {
            throw new ForbiddenHttpException('You do not have permission to view this subscription.');
        }
        $row = [
            'id' => $sub->id,
            'user_id' => $sub->user_id,
            'plan_id' => $sub->plan_id,
            'status' => $sub->status,
            'type' => $sub->type,
            'trial_end_at' => $sub->trial_end_at,
            'created_at' => $sub->created_at,
            'updated_at' => $sub->updated_at,
        ];
        return $this->render('view', ['row' => $row]);
    }

    public function actionCancel($id)
    {
        $sub = Subscription::findOne((int)$id);
        if (!$sub) {
            throw new NotFoundHttpException('Subscription not found');
        }
        // Owner or RBAC permission
        if ($sub->user_id != Yii::$app->user->id && !Yii::$app->user->can('subscription.cancel')) {
            throw new ForbiddenHttpException('You do not have permission to cancel this subscription.');
        }
        $service = new SubscriptionService();
        if ($service->cancel($sub)) {
            Yii::$app->session->setFlash('success', 'Subscription cancelled');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to cancel subscription');
        }
        return $this->redirect(['index']);
    }
}
