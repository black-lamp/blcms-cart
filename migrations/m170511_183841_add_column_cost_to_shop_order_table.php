<?php

use yii\db\Migration;

class m170511_183841_add_column_cost_to_shop_order_table extends Migration
{
    public function up()
    {
        $this->addColumn('shop_order', 'cost', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('shop_order', 'cost');
    }
}
