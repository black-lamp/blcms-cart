<?php
namespace bl\cms\cart;

use Yii;
use yii\base\Component;
use yii\web\ForbiddenHttpException;

/**
 * This is the component class CartComponent for "Blcms-shop" module.
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 * @property Order $order
 * @property string $status
 */

class CartComponent extends Component
{

    /*Component configuration*/
    public $emailNotifications = true;
    public $sendTo = [];
    public $saveToDataBase = true;

    /*Session key*/
    const SESSION_KEY = 'shop_order';

    /*Order status constants*/
    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_CANCELED = 'canceled';


    public function addToCart($productId, $count) {

        if ($this->saveToDataBase) {
            $this->saveProductToDataBase($productId, $count);
        }
        else {
            $this->saveProductToSession($productId, $count);
        }
    }

    private function saveProductToDataBase($productId, $count) {
        if (!\Yii::$app->user->isGuest) {

            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => self::STATUS_INCOMPLETE]);
            if (empty($order)) {
                $order = new Order();
                $order->user_id = \Yii::$app->user->id;
                $order->status = self::STATUS_INCOMPLETE;
                $order->save();
            }

            $orderProduct = OrderProduct::findOne(['product_id' => $productId, 'order_id' => $order->id]);
            if (!$orderProduct) {
                $orderProduct = new OrderProduct();
                $orderProduct->product_id = $productId;
                $orderProduct->order_id = $this->order->id;
            }

            $orderProduct->count += $count;
            $orderProduct->save();
        }
        else throw new ForbiddenHttpException();
    }

    private function saveProductToSession($productId, $count) {
        if (!Yii::$app->session->has(self::SESSION_KEY)) {
            $products = Yii::$app->session->get(self::SESSION_KEY);


            $productInSession = array_filter($products, function($innerArray){
                global $needle;
                //return in_array($needle, $innerArray);    //Поиск по всему массиву
                return ($innerArray[0] == $needle); //Поиск по первому значению
            });

        }
        else {
            Yii::$app->session->set(self::SESSION_KEY, [$productId, $count]);
        }
    }
}