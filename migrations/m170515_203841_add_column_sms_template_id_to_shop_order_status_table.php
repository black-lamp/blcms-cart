<?php

use yii\db\Migration;

class m170515_203841_add_column_sms_template_id_to_shop_order_status_table extends Migration
{
    public function up()
    {
        $this->addColumn('shop_order_status', 'sms_template_id', $this->integer());
        $this->addForeignKey('sms_template_id:email_template_id',
            'shop_order_status', 'sms_template_id', 'email_template', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('sms_template_id:email_template_id', 'shop_order_status');
        $this->dropColumn('shop_order_status', 'sms_template_id');
    }
}
