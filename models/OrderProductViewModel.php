<?php
namespace bl\cms\cart\models;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class OrderProductViewModel extends OrderProduct
{
    public $product_id;
    public $combination_id;
    public $count;
}