<?php

use yii\db\Migration;

/**
 * Handles the creation of table `passive_income`.
 */
class m240509_210714_create_passive_income_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('passive_income', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'level' => $this->integer(2)->null(),
            'slot_active' => $this->integer(2)->defaultValue(1)->null(),
            'amount' => $this->money()->null(),
            'months' => $this->integer()->null(),
            'activation_date' => $this->date()->null(),
            'end_date' => $this->date()->null(),
            'next_payment_date' => $this->date()->null(),
            'payments_done' => $this->integer()->null(),
            'complete' => $this->integer(1)->defaultValue(0)->null(),
            'tour_id' => $this->integer(1)->null(),
            'phone' => $this->integer(1)->defaultValue(0)->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->null(),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->null(),
        ]);

        $this->createIndex('idx-passive_income-user_id', 'passive_income', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('passive_income');
    }
}
