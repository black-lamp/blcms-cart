<?php
namespace bl\cms\cart\common\classes;
use yii\base\Object;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
abstract class CartSumAdjustment extends Object
{
    /**
     * @param $sum
     * @return int
     */
    abstract function countAdjustment($sum);
}