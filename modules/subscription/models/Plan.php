<?php
namespace app\modules\subscription\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property float|null $price
 */
class Plan extends ActiveRecord
{
    public static function tableName(){ return '{{%plan}}'; }
}
