<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%emerald}}`.
 */
class m240512_062109_create_emerald_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('travel_main', [
            'id'         => $this->primaryKey(),
            'id_user'    => $this->integer(11)->notNull()->defaultValue(0),
            'level'      => $this->integer(11)->notNull()->defaultValue(0),
            'created_at' => $this->integer(11)->notNull()->defaultValue(0),
            'updated_at' => $this->integer(11)->notNull()->defaultValue(0),
        ]);

        $tm = time();
        for ($i = 1; $i < 6; $i++) { // Замените 100 на количество записей, которые вы хотите добавить
            $this->insert('emerald_users', [
                'id' => 1,
                'id_user' => 1,
                'level' => 1,
                'created_at' => 1,
                'updated_at' => 1,
            ]);
        }
        $this->db->createCommand('INSERT INTO travel_main (id_user, level, created_at, updated_at) 
                                                   VALUES (1, 1, ' . $tm . ', ' . $tm . '),
                                                          (1, 2, ' . $tm . ', ' . $tm . '),
                                                          (1, 3, ' . $tm . ', ' . $tm . '),
                                                          (1, 4, ' . $tm . ', ' . $tm . '),
                                                          (1, 5, ' . $tm . ', ' . $tm . '),
                                                          (1, 6, ' . $tm . ', ' . $tm . '),
                                                          (1, 7, ' . $tm . ', ' . $tm . '),
                                                          (1, 8, ' . $tm . ', ' . $tm . ')')->execute();

        $this->createIndex('idx1', 'travel_main', ['id_user']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%emerald}}');
    }
}
