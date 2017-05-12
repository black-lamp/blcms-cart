<?php
namespace bl\cms\cart\common\classes;

use yii\base\Model;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
abstract class OrderPaymentDiscountCounter extends Model
{
    public abstract function getDiscount();
}