<?php
namespace bl\cms\cart;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\web\ForbiddenHttpException;
use bl\cms\cart\models\Order;
use bl\cms\cart\models\OrderProduct;

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
    public $sender;
    public $mailDir = '@vendor/black-lamp/blcms-cart/views/mail/';

    /*Session key*/
    const SESSION_KEY = 'shop_order';

    /*Order status constants*/
    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_CANCELED = 'canceled';


    public function __construct($config)
    {
        $this->emailNotifications = $config['emailNotifications'];
        $this->sendTo = $config['sendTo'];
        $this->saveToDataBase = $config['saveToDataBase'];
        $this->sender = $config['sender'];

        parent::__construct();
    }


    public function add($productId, $count) {

        if ($this->saveToDataBase) {
            $this->saveProductToDataBase($productId, $count);
        }
        else {
            $this->saveProductToSession($productId, $count);
        }
    }

    private function saveProductToDataBase($productId, $count) {
        if (!\Yii::$app->user->isGuest) {

            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => self::STATUS_INCOMPLETE])->one();
            if (empty($order)) {
                $order = new Order();
                $order->user_id = \Yii::$app->user->id;
                $order->status = self::STATUS_INCOMPLETE;
                $order->save();
            }
            $orderProduct = OrderProduct::findOne(['product_id' => $productId, 'order_id' => $order->id]);
            if (empty($orderProduct)) {
                $orderProduct = new OrderProduct();
                $orderProduct->product_id = $productId;
                $orderProduct->order_id = $order->id;
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

    public function getOrderItems() {
        $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => $this::STATUS_INCOMPLETE])->one();
        $order = OrderProduct::find()->where(['order_id' => $order->id])->one();
        return $order;
    }

    public function getAllUserOrders() {
        $orders = Order::find()->where(['user_id' => \Yii::$app->user->id])->all();
        return $orders;
    }

    public function removeItem($id) {
        $orderProduct = OrderProduct::findOne($id);
        $orderProduct->delete();
    }

    public function makeOrder($customerData) {
        $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => self::STATUS_INCOMPLETE])->one();
        if (empty($order)) {
            Yii::$app->session->setFlash('error', \Yii::t('shop', 'You did not add to cart no one product.'));
            return false;
        }
        else {
            $order->first_name = $customerData['Order']['first_name'];
            $order->last_name = $customerData['Order']['last_name'];
            $order->email = $customerData['Order']['email'];
            $order->phone = $customerData['Order']['phone'];
            $order->address = $customerData['Order']['address'];
            $order->status = self::STATUS_CONFIRMED;
            if ($order->validate()) {
                $order->save();

                if ($this->emailNotifications) {
                    $this->sendMail($order);
                }
            }
            return true;
        }
    }

    private function sendMail($order) {
        $products = OrderProduct::find()->where(['order_id' => $order->id])->all();

        if (!empty($this->sender) && !empty($order)) {
            try {
                foreach ($this->sendTo as $admin) {
                    Yii::$app->mailer->compose($this->mailDir . 'new-order',
                        ['order' => $order, 'products' => $products])
                        ->setFrom($this->sender)
                        ->setTo($admin)
                        ->setSubject(Yii::t('shop', 'New order'))
                        ->send();
                }

                Yii::$app->mailer->compose($this->mailDir . 'order-success',
                    ['products' => $products])
                    ->setFrom($this->sender)
                    ->setTo($order->email)
                    ->setSubject(Yii::t('shop', 'Success order'))
                    ->send();
            }
            catch(Exception $ex) {
//                die(var_dump($ex));
            }
        }
    }
}