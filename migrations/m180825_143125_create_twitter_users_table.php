<?php

use yii\db\Migration;

/**
 * Handles the creation of table `twitter_users`.
 */
class m180825_143125_create_twitter_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('twitter_users', [
            'id' => $this->primaryKey(),
            'src_id' => $this->string(32)->notNull(),
            'user' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('twitter_users');
    }
}
