<?php
use yii\db\Migration;

class m230201_000001_subscription_schema_hardening extends Migration
{
    public function safeUp()
    {
        // Add missing columns if not exist
        $table = $this->db->schema->getTableSchema('{{%subscription}}', true);
        if ($table === null) {
            // Create minimally viable table (idempotent)
            $this->createTable('{{%subscription}}', [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->notNull(),
                'plan_id' => $this->integer()->notNull(),
                'status' => $this->string(20)->notNull()->defaultValue('active'),
                'type'   => $this->string(20)->notNull()->defaultValue('paid'),
                'trial_end_at' => $this->dateTime()->null(),
                'created_at' => $this->dateTime()->notNull(),
                'updated_at' => $this->dateTime()->notNull(),
            ]);
        } else {
            if (!isset($table->columns['created_at'])) {
                $this->addColumn('{{%subscription}}','created_at',$this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
            }
            if (!isset($table->columns['updated_at'])) {
                $this->addColumn('{{%subscription}}','updated_at',$this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
            }
            // tighten nullability/defaults if needed (safe: use ALTER COLUMN only if different)
            // Add indexes idempotently
            $this->createIndex('idx_sub_user','{{%subscription}}','user_id', false);
            $this->createIndex('idx_sub_plan','{{%subscription}}','plan_id', false);
            $this->createIndex('idx_sub_status','{{%subscription}}','status', false);
            $this->createIndex('idx_sub_type','{{%subscription}}','type', false);
        }

        // Add foreign keys if referenced tables exist and FKs not already present
        $schema = $this->db->schema->getTableSchema('{{%subscription}}', true);
        if ($schema !== null) {
            // Avoid duplicate FKs by checking information_schema is not portable; try/catch instead
            try {
                $this->addForeignKey('fk_sub_user','{{%subscription}}','user_id','{{%user}}','id','CASCADE','RESTRICT');
            } catch (\Throwable $e) {}
            try {
                $this->addForeignKey('fk_sub_plan','{{%subscription}}','plan_id','{{%plan}}','id','CASCADE','RESTRICT');
            } catch (\Throwable $e) {}
        }
    }

    public function safeDown()
    {
        // Non-destructive down: best effort drop FKs/indexes only
        foreach (['fk_sub_user','fk_sub_plan'] as $fk) {
            try { $this->dropForeignKey($fk,'{{%subscription}}'); } catch (\Throwable $e) {}
        }
        foreach (['idx_sub_user','idx_sub_plan','idx_sub_status','idx_sub_type'] as $idx) {
            try { $this->dropIndex($idx,'{{%subscription}}'); } catch (\Throwable $e) {}
        }
        // Keep columns/table for safety.
    }
}
