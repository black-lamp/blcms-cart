<?php
namespace bl\cms\cart\backend\events;

use bl\cms\cart\models\Order;
use yii\base\Event;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class OrderEvent extends Event
{
    /**
     * @var Order
     */
    public $model;

}