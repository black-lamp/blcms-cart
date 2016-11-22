<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

namespace bl\cms\cart\frontend\Events;


use yii\base\Event;

class OrderInfoEvent extends Event
{
    public $user_id;
}