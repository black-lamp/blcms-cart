<?php
namespace bl\cms\cart;

use bl\cms\shop\common\entities\Product;
use Yii;
use yii\base\Component;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class CartComponent extends Component
{
    const SESSION_KEY = 'order_id';

    public static $emailNotifications = true;
    public $sendTo = [];
    public $saveToDataBase = true;

    public function saveToCart($productId, $count) {

        if (!\Yii::$app->user->isGuest) {
            $product = OrderProduct::findOne(['product_id' => $productId, 'order_id' => $this->order->id]);
            if (!$product) {
                $product = new Product();
            }
            $product->product_id = $productId;
            $product->order_id = $this->order->id;
            $product->count += $count;
        }


        return $product->save();

    }
}