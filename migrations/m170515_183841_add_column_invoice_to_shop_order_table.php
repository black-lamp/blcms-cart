<?php

use yii\db\Migration;

class m170515_183841_add_column_invoice_to_shop_order_table extends Migration
{
    public function up()
    {
        $this->addColumn('shop_order', 'invoice', $this->string());
    }

    public function down()
    {
        $this->dropColumn('shop_order', 'invoice');
    }
}
