<?php
namespace app\modules\subscription\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * @property int $id
 * @property int $user_id
 * @property int $plan_id
 * @property string $status
 * @property string $type
 * @property string|null $trial_end_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property-read User $user
 * @property-read Plan $plan
 */
class Subscription extends ActiveRecord
{
    public static function tableName(){ return '{{%subscription}}'; }

    public function behaviors()
    {
        // created_at/updated_at will be maintained automatically
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    public function rules()
    {
        return [
            [['user_id','plan_id'], 'integer'],
            [['status','type'], 'string', 'max' => 20],
            [['status','type'], 'required'],
            [['trial_end_at','created_at','updated_at'], 'safe'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getPlan()
    {
        return $this->hasOne(Plan::class, ['id' => 'plan_id']);
    }

    public function scopeActive($query)
    {
        return $query->andWhere(['status' => 'active']);
    }

    public function isTrial(): bool
    {
        return $this->type === 'trial';
    }

    public static function findActiveByUser(int $userId): ?self
    {
        return static::find()
            ->where(['user_id' => $userId, 'status' => 'active'])
            ->limit(1)
            ->one();
    }
}
