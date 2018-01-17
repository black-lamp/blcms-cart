<?php

use yii\db\Migration;

class m180117_024215_shop_order_add_column_delivery_number extends Migration
{
    public function up()
    {
        $this->addColumn('shop_order', 'delivery_number', $this->string());
    }

    public function down()
    {
        $this->dropColumn('shop_order', 'delivery_number');
    }
}
