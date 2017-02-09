<?php

use yii\db\Migration;

class m170209_160321_add_username_column extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'username', $this->string());
        $this->createIndex('user_unique_username', '{{%user}}', 'username', true);

    }

    public function down()
    {
        echo "m170209_160321_add_username_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
