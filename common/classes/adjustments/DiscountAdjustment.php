<?php
namespace bl\cms\cart\common\classes\adjustments;

use bl\cms\cart\common\classes\CartSumAdjustment;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class DiscountAdjustment extends CartSumAdjustment
{
    public $percent;

    /**
     * @param $sum
     * @return int
     */
    function countAdjustment($sum)
    {
        $adjustment = 0;

        if(!empty($this->percent)) {
            if($this->percent > 0) {
                $adjustment = -($this->percent / 100) * $sum;
            }
        }

        return $adjustment;
    }
}