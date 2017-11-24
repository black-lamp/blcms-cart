<?php

use yii\db\Migration;

class m171124_183841_add_column_user_comment_to_shop_order_table extends Migration
{
    public function up()
    {
        $this->addColumn('shop_order', 'user_comment', $this->text());
    }

    public function down()
    {
        $this->dropColumn('shop_order', 'user_comment');
    }
}
