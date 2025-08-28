<?php
/**
 * View after refactor: eager-loaded relations avoid N+1.
 * Keep the rendered markup identical.
 */
/* @var $subs app\modules\subscription\models\Subscription[] */
use Yii;
?>
<h1>Subscriptions</h1>
<table border="1" cellpadding="6">
    <tr>
        <th>ID</th><th>User</th><th>Plan</th><th>Status</th><th>Type</th><th>Trial End</th>
    </tr>
    <?php foreach($subs as $sub): ?>
        <tr>
            <td><?= (int)$sub->id ?></td>
            <td><?= $sub->user ? htmlspecialchars($sub->user->id.'#'.$sub->user->username) : '-' ?></td>
            <td><?= $sub->plan ? htmlspecialchars($sub->plan->name ?? ('#'.$sub->plan_id)) : '-' ?></td>
            <td><?= htmlspecialchars($sub->status) ?></td>
            <td><?= htmlspecialchars($sub->type) ?></td>
            <td><?= htmlspecialchars($sub->trial_end_at) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
$total = \app\modules\subscription\models\Subscription::find()->count();
echo "<p>Total subscriptions: ".(int)$total."</p>";
?>
