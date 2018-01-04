<?php

use yii\db\Migration;

class m180104_024215_shop_order_product_add_price_currency extends Migration
{
    public function up()
    {
        $this->addColumn('shop_order_product', 'base_price', $this->float());
        $this->addColumn('shop_order_product', 'base_sum', $this->float());
    }

    public function down()
    {
        $this->dropColumn('shop_order_product', 'base_price');
        $this->dropColumn('shop_order_product', 'base_sum');
    }
}
