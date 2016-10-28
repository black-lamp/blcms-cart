<?php
namespace bl\cms\cart;

use bl\cms\shop\common\components\user\models\Profile;
use bl\cms\shop\common\components\user\models\UserAddress;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\Url;
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
    const STATUS_INCOMPLETE = 1;
    const STATUS_CONFIRMED = 2;


    public function __construct($config)
    {
        $this->emailNotifications = $config['emailNotifications'];
        $this->sendTo = $config['sendTo'];
        $this->saveToDataBase = $config['saveToDataBase'];
        $this->sender = $config['sender'];

        parent::__construct();
    }


    public function add($productId, $count, $priceId = null) {

        if ($this->saveToDataBase && !\Yii::$app->user->isGuest) {
            $this->saveProductToDataBase($productId, $count, $priceId);
        }
        else {
            $this->saveProductToSession($productId, $count, $priceId);
        }
    }

    private function saveProductToDataBase($productId, $count, $priceId = null) {

        if (!\Yii::$app->user->isGuest) {

            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => self::STATUS_INCOMPLETE])->one();
            if (empty($order)) {
                $order = new Order();
                $order->user_id = \Yii::$app->user->id;
                $order->status = self::STATUS_INCOMPLETE;
                if ($order->validate()) {
                    $order->save();
                }
            }

            $orderProduct = (!empty($priceId)) ? OrderProduct::findOne(['price_id' => $priceId, 'order_id' => $order->id]) :
                OrderProduct::findOne(['product_id' => $productId, 'order_id' => $order->id]);

            if (empty($orderProduct)) {
                $orderProduct = new OrderProduct();
                $orderProduct->product_id = $productId;

                if (!empty($priceId)) {
                    $orderProduct->price_id = $priceId;
                }
                $orderProduct->order_id = $order->id;
            }

            $orderProduct->count += $count;
            if ($order->validate()) {
                $orderProduct->save();
            }
        }
        else throw new ForbiddenHttpException();
    }

    private function saveProductToSession($productId, $count, $priceId = null) {
        $session = Yii::$app->session;
        if ($session->has(self::SESSION_KEY)) {

            $products = $session[self::SESSION_KEY];

            if (!empty($products)) {
                foreach ($products as $key => $product) {
                    if ($product['id'] == $productId) {
                        $products[$key]['count'] += $count;
                        break;
                    }
                    else {
                        if (count($products) - 1 == $key) {
                            $products[] = ['id' => $productId, 'count' => $count, 'priceId' => $priceId];
                        }
                    }
                }
                $session[self::SESSION_KEY] = $products;

            }

        }
        else {
            $session[self::SESSION_KEY] = [['id' => $productId, 'count' => $count, 'priceId' => $priceId]];
        }
    }

    public function getOrderItems() {
        if (\Yii::$app->user->isGuest) {
            $session = \Yii::$app->session;
            $products = $session[self::SESSION_KEY];
        }
        else {

            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => self::STATUS_INCOMPLETE])->one();
            if (!empty($order)) {
                $products = OrderProduct::find()->asArray()->where(['order_id' => $order->id])->all();

            }
            else return false;

        }
        return $products;

    }

    public function getAllUserOrders() {
        $orders = Order::find()->where(['user_id' => \Yii::$app->user->id])->all();
        return $orders;
    }

    public function removeItem($id) {
        if (!\Yii::$app->user->isGuest) {
            $orderProduct = OrderProduct::findOne($id);
            $orderProduct->delete();
        }
        else {
            $session = Yii::$app->session;
            if ($session->has(self::SESSION_KEY)) {
                $products = $session[self::SESSION_KEY];
                foreach ($products as $key => $product) {
                   if ($product['id'] == $id) {
                       $session->remove([self::SESSION_KEY][$key]);
                   }
                }
            }
        }

    }

    public function makeOrder($customerData) {
        $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => self::STATUS_INCOMPLETE])->one();
        $user = $order->user;

        if (empty($order)) {
            $order = new Order();
        }

        $order->status = self::STATUS_CONFIRMED;

        $profile = Profile::find()->where(['user_id' => \Yii::$app->user->id])->one();

        if ($profile->load($customerData)) {
            if ($profile->validate()) {
                $profile->save();
            }
            else throw new Exception($profile->errors);
        }

        if (empty($order->address_id)) {
            $address = new UserAddress();

            if ($address->load($customerData)) {
                $address->user_profile_id = \Yii::$app->user->identity->profile->id;

                if ($address->validate()) {
                    $address->save();
                    $order->address_id = $address->id;
                    $order->user_id = (!empty($order->user_id)) ? $order->user_id : \Yii::$app->user->id;
                    $order->save();
                    $this->sendMail($profile, $products = null, $user, $order, $address);
                    return true;
                }

            }
        }
        else {
            $order->user_id = (!empty($order->user_id)) ? $order->user_id : \Yii::$app->user->id;
            $order->save();
            $this->sendMail($profile, $products = null, $user, $order, $address = null);
            return true;
        }


        return false;
    }

    private function sendMail($profile = null, $products = null, $user = null, $order = null, $address = null) {
        $products = OrderProduct::find()->where(['order_id' => $order->id])->all();

        if (!empty($this->sender) && !empty($order)) {
            try {
                foreach ($this->sendTo as $admin) {
                    Yii::$app->mailer->compose($this->mailDir . 'new-order',
                        ['products' => $products, 'user' => $user, 'profile' => $profile, 'order' => $order, 'address' => $address])
                        ->setFrom($this->sender)
                        ->setTo($admin)
                        ->setSubject(Yii::t('shop', 'New order'))
                        ->send();
                }

                Yii::$app->mailer->compose($this->mailDir . 'order-success',
                    ['products' => $products, 'user' => $user, 'profile' => $profile, 'order' => $order, 'address' => $address])
                    ->setFrom($this->sender)
                    ->setTo($order->email)
                    ->setSubject(Yii::t('shop', 'Success order'))
                    ->send();
            }
            catch(Exception $ex) {
            }
        }
    }

    public function clearCart() {
        if (\Yii::$app->user->isGuest) {
            $session = \Yii::$app->session;
            $session->remove(self::SESSION_KEY);
        }
        else {
            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => self::STATUS_INCOMPLETE])->one();
            $order->deleteAll();
        }
    }

    public function transportSessionDataToDB() {
        $session = Yii::$app->session;

        if ($session->has(self::SESSION_KEY)) {

            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => self::STATUS_INCOMPLETE])->one();
            if (empty($order)) {
                $order = new Order();
                $order->user_id = \Yii::$app->user->id;
                $order->status = self::STATUS_INCOMPLETE;
                if ($order->validate()) {
                    $order->save();
                }
            }

            $products = $session[self::SESSION_KEY];

            foreach ($products as $product) {

                $orderProduct = OrderProduct::find()
                    ->where(['product_id' => $product['id'], 'price_id' => $product['priceId'], 'order_id' => $order->id])->one();
                if (empty($orderProduct)) {

                    $orderProduct = new OrderProduct;

                    $orderProduct->order_id = $order->id;
                    $orderProduct->product_id = $product['id'];
                    $orderProduct->price_id = $product['priceId'];
                    $orderProduct->count = $product['count'];
                }
                else {
                    $orderProduct->count += $product['count'];
                }

                if ($orderProduct->validate()) {

                    $orderProduct->save();
                }
                else throw new Exception($orderProduct->errors);

            }

        }
        \Yii::$app->getResponse()->redirect(Url::toRoute('/shop/cart/show'));
        \Yii::$app->end();
    }
}