<?php

use yii\db\Migration;

class m160923_135625_add_orders_table extends Migration
{
    public function up()
    {
        $this->createTable('shop_order', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(255)->notNull(),
            'last_name' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'phone' => $this->string(255)->notNull(),
            'address' => $this->integer(11)->notNull(),
            'status' => $this->smallInteger(1)->defaultValue(0),
        ]);

        $this->createTable('shop_order_product', [
            'product_id' => $this->integer(11)->notNull(),
            'order_id' => $this->integer(11)->notNull(),
            'count' => $this->integer(11)->defaultValue(1),
        ]);

        $this->addForeignKey('order_product_product_id_product_id', 'shop_order_product', 'product_id', 'shop_product', 'id');
        $this->addForeignKey('order_product_order_id_order_id', 'shop_order_product', 'order_id', 'shop_order', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('order_product_order_id_order_id', 'shop_order_product');
        $this->dropForeignKey('order_product_product_id_product_id', 'shop_order_product');

        $this->dropTable('shop_order_product');
        $this->dropTable('shop_orders');
    }
}
