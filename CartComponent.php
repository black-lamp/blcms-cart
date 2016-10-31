<?php
namespace bl\cms\cart;

use bl\cms\cart\models\OrderStatus;
use bl\cms\shop\common\components\user\models\Profile;
use bl\cms\shop\common\components\user\models\User;
use bl\cms\shop\common\components\user\models\UserAddress;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use bl\cms\cart\models\Order;
use bl\cms\cart\models\OrderProduct;

/**
 * This is the component class CartComponent for "Blcms-shop" module.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 */
class CartComponent extends Component
{

    /**
     * @var bool
     * Enabling sending e-mails
     */
    public $emailNotifications = true;

    /**
     * @var array
     * List of managers e-mails to which notification will be sent.
     */
    public $sendTo = [];

    /**
     * @var bool
     * Enabling saving order products to database. If false, order products will only be stored in the session.
     */
    public $saveToDataBase = true;

    /**
     * @var string
     * From this address e-mails will be sent.
     */
    public $sender;

    /**
     * @var string
     * The directory with e-mail views.
     */
    public $mailDir = '@vendor/black-lamp/blcms-cart/views/mail/';

    /**
     * @var integer
     * The minimal order unique id.
     */
    public $minOrderUid = 10000000;
    /**
     * @var integer
     * The maximal order unique id.
     */
    public $maxOrderUid = 99999999;/**
     * @var integer
     * The order unique id prefix.
     */
    public $uidPrefix = '';

    /*Session key of order*/
    const SESSION_KEY = 'shop_order';
    /*Session key of total cost*/
    const TOTAL_COST_KEY = 'shop_order_total_cost';


    /**
     * Adds product to cart.
     *
     * @param integer $productId
     * @param integer $count
     * @param integer $priceId
     */
    public function add($productId, $count, $priceId = null) {

        if ($this->saveToDataBase && !\Yii::$app->user->isGuest) {
            $this->saveProductToDataBase($productId, $count, $priceId);
        }
        else {
            $this->saveProductToSession($productId, $count, $priceId);
        }
    }

    /**
     * Saves product to database if the corresponding property is true.
     *
     * @param integer $productId
     * @param integer $count
     * @param integer $priceId
     * @throws ForbiddenHttpException
     */
    private function saveProductToDataBase($productId, $count, $priceId = null) {

        if ($this->saveToDataBase === true && !\Yii::$app->user->isGuest) {

            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])->one();
            if (empty($order)) {
                $order = new Order();
                $order->uid = $this->generateUnicId($this->uidPrefix, $this->minOrderUid, $this->maxOrderUid);
                $order->user_id = \Yii::$app->user->id;
                $order->status = OrderStatus::STATUS_INCOMPLETE;
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

    /**
     * Saves product to session if user is guest or if the $saveToDataBase property is false.
     *
     * @param integer $productId
     * @param integer $count
     * @param integer $priceId
     * @return boolean
     */
    private function saveProductToSession($productId, $count, $priceId = null) {
        if (!empty($productId) && (!empty($count))) {
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

            if (empty($priceId)) {
                $product = Product::findOne($productId);
                $price = $product->price;
            }
            else {
                $price = ProductPrice::findOne($priceId)->salePrice;
            }
            if (!$session->has(self::TOTAL_COST_KEY)) {
                $session[self::TOTAL_COST_KEY] = 0;
            }
            $session[self::TOTAL_COST_KEY] += $price * $count;
            return true;
        }
        else return false;
    }

    /**
     * Gets order items.
     *
     * @return array|bool|mixed|\yii\db\ActiveRecord[]
     */
    public function getOrderItems() {
        if (\Yii::$app->user->isGuest) {
            $session = \Yii::$app->session;
            $products = $session[self::SESSION_KEY];
        }
        else {

            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])->one();
            if (!empty($order)) {
                $products = OrderProduct::find()->asArray()->where(['order_id' => $order->id])->all();

            }
            else return false;

        }
        return $products;

    }

    /**
     * Gets all user orders from database.
     *
     * @return bool|\yii\db\ActiveRecord[]
     */
    public function getAllUserOrders() {
        if (!\Yii::$app->user->isGuest && $this->saveToDataBase === true) {
            $orders = Order::find()->where(['user_id' => \Yii::$app->user->id])->all();
            return $orders;
        }
        else return false;
    }

    /**
     * Removes item from order.
     *
     * @param $id
     */
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

                       if (!empty($session[self::SESSION_KEY][$key]['priceId'])) {
                           $price = ProductPrice::findOne($session[self::SESSION_KEY][$key]['priceId'])->salePrice;
                       }
                       else $price = Product::findOne($id)->price;
                       $session[self::TOTAL_COST_KEY] -= $price * $session[self::SESSION_KEY][$key]['count'];

                       unset($_SESSION[self::SESSION_KEY][$key]);
                   }
                }
            }
        }
    }

    /**
     * @param $customerData
     * @return bool
     * @throws Exception
     */
    public function makeOrder($customerData) {
        $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])->one();
        $user = $order->user;

        $order->status = OrderStatus::STATUS_CONFIRMED;

        $profile = Profile::find()->where(['user_id' => \Yii::$app->user->id])->one();

        if ($order->load(Yii::$app->request->post()) && $profile->load($customerData)) {
            if ($profile->validate() && $order->validate()) {
                $profile->save();
            }
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

    /**
     * @param null|Profile $profile
     * @param null|OrderProduct $products
     * @param null|User $user
     * @param null|Order $order
     * @param null|UserAddress $address
     */
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

    /**
     * Clears cart.
     */
    public function clearCart() {
        if (!\Yii::$app->user->isGuest && $this->saveToDataBase === true) {
            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])->one();
            $order->deleteAll();
        }
        else {
            $session = \Yii::$app->session;
            $session->remove(self::SESSION_KEY);
            $session->remove(self::TOTAL_COST_KEY);
        }
    }

    /**
     * Moves order products from session to database if $saveToDataBase property is true.
     *
     * @throws Exception
     */
    public function transportSessionDataToDB() {
        if ($this->saveToDataBase === true) {
            $session = Yii::$app->session;

            if ($session->has(self::SESSION_KEY)) {

                $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])->one();
                if (empty($order)) {
                    $order = new Order();
                    $order->user_id = \Yii::$app->user->id;
                    $order->status = OrderStatus::STATUS_INCOMPLETE;
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
            \Yii::$app->getResponse()->redirect(Url::toRoute('/cart'));
            \Yii::$app->end();
        }
    }

    public function getTotalCost() {
        if (Yii::$app->user->isGuest) {
            $session = Yii::$app->session;
            if ($session->has(self::TOTAL_COST_KEY)) {
                $totalCost = $session[self::TOTAL_COST_KEY];
                return $totalCost;
            }
            else return false;
        }

        else {
            $order = Order::find()
                ->where(['user_id' => Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])
                ->one();
            if (!empty($order)) {
                $orderProducts = OrderProduct::find()->where(['order_id' => $order->id])->all();
                $totalCost = 0;
                if (!empty($orderProducts)) {
                    foreach ($orderProducts as $product) {
                        $totalCost += $product->count * $product->price;
                    }
                }
                return $totalCost;
            }
        }
    }

    public function generateUnicId($prefix, $min, $max) {
        $prefix = (!empty($prefix)) ? $prefix : '';

        $id = random_int($min, $max);
        $order = Order::find()->where(['uid' => $id])->one();
        if (empty($order)) {
            return $prefix . $id;
        }
        else $this->generateUnicId($prefix, $min, $max);
    }
}