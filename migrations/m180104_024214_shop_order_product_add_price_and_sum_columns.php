<?php

use yii\db\Migration;

class m180104_024214_shop_order_product_add_price_and_sum_columns extends Migration
{
    public function up()
    {
        $this->addColumn('shop_order_product', 'price', $this->float());
        $this->addColumn('shop_order_product', 'sum', $this->float());
    }

    public function down()
    {
        $this->dropColumn('shop_order_product', 'price');
        $this->dropColumn('shop_order_product', 'sum');
    }
}
