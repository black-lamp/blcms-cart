<?php
namespace bl\cms\cart\frontend\events;

use yii\base\Event;

class MakeOrderSuccessEvent extends Event
{
    /**
     * @var array
     */
    public $orderResult;
}