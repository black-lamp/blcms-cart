<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

namespace bl\cms\cart\widgets;

use bl\cms\cart\models\Order;
use bl\cms\cart\models\OrderStatus;
use yii\base\Widget;

class OrderCounter extends Widget
{

    public function init()
    {
    }

    public function run()
    {
        $count = Order::find()->where(['status' => OrderStatus::STATUS_CONFIRMED])->count();
        return $this->render('order-counter', [
            'count' => $count
        ]);
    }
}