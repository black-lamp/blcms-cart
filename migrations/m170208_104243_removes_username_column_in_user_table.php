<?php

use yii\db\Migration;

class m170208_104243_removes_username_column_in_user_table extends Migration
{
    public function up()
    {
        $this->dropColumn('user', 'username');
    }

    public function down()
    {
        echo "m170208_104243_removes_username_column_in_user_table cannot be reverted.\n";

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
