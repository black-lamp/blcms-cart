<?php
namespace bl\cms\cart;

use Yii;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use yii\base\{Component, Exception};
use yii\db\{ActiveRecord, Expression};
use bl\cms\cart\frontend\events\OrderInfoEvent;
use bl\cms\cart\models\{
    Order, OrderProductAdditionalProduct, OrderStatus, OrderProduct
};
use bl\cms\shop\common\components\user\models\User;
use bl\cms\cart\common\components\user\models\{Profile, UserAddress};
use bl\cms\shop\common\entities\{Product, Combination, CombinationAttribute, Price};

/**
 * This is the component class CartComponent for "Blcms-shop" module.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CartComponent extends Component
{

    /**
     * If enabled, prices will be gotten from shop_product_combination table.
     * Else from shop_product_price.
     *
     * @var bool
     */
    public $enableGetPricesFromCombinations = false;

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
     * @var integer
     * The minimal order unique id.
     */
    public $minOrderUid = 10000000;
    /**
     * @var integer
     * The maximal order unique id.
     */
    public $maxOrderUid = 99999999;
    /**
     * @var integer
     * The order unique id prefix.
     */
    public $uidPrefix = '';

    public $enablePayment = false;

    /*Session key of order*/
    const SESSION_KEY = 'shop_order';
    /*Session key of total cost*/
    const TOTAL_COST_KEY = 'shop_order_total_cost';

    const EVENT_BEFORE_GET_ORDER = 'before-get-order';
    const EVENT_BEFORE_GET_ORDER_FROM_DB = 'before-get-order-from-db';

    /**
     * Adds product to cart.
     *
     * @param integer $productId
     * @param integer $count
     * @param integer|null $priceId
     * @param array|null $attributesAndValues
     */
    public function add($productId, $count, $priceId = null, $attributesAndValues = null, $additionalProducts = null)
    {
        if (!empty($attributesAndValues)) {
            $attributesAndValues = Json::decode($attributesAndValues);
        }
        if ($this->saveToDataBase && !\Yii::$app->user->isGuest) {
            $this->saveProductToDataBase($productId, $count, $priceId, $attributesAndValues, $additionalProducts);
        } else {
            $this->saveProductToSession($productId, $count, $priceId, $attributesAndValues, $additionalProducts);
        }
    }

    /**
     * Saves product to database if the corresponding property is true.
     *
     * @param integer $productId
     * @param integer $count
     * @param integer $priceId
     * @param array|null $attributesAndValues
     * @param array|null $additionalProducts
     * @throws ForbiddenHttpException
     * @throws Exception
     */
    private function saveProductToDataBase($productId, $count, $priceId = null, $attributesAndValues = null, $additionalProducts = null)
    {
        if ($this->saveToDataBase && !\Yii::$app->user->isGuest) {

            $order = $this->getIncompleteOrderFromDB();

            if ($this->enableGetPricesFromCombinations) {
                if (!empty($attributesAndValues)) {
                    $combination = $this->getCombination($attributesAndValues, $productId);
                    if (!empty($combination)) {
                        $orderProduct = $this->getOrderProductByCombinationId($order->id, $productId, $combination->id);
                    } else throw new Exception(\Yii::t('cart', 'Such attributes combination does not exist'));
                } else {
                    $orderProduct = $this->getOrderProductByPriceId($order->id, $productId, $priceId);
                }
            } else {
                $orderProduct = $this->getOrderProductByPriceId($order->id, $productId, $priceId);
            }

            $orderProduct->count += $count;
            if ($orderProduct->validate()) {
                $orderProduct->save();

                if (!empty($additionalProducts)) {
                    foreach ($additionalProducts as $additionalProduct) {
                        $orderProductAdditionalProduct = OrderProductAdditionalProduct::find()
                            ->where(['order_product_id' => $orderProduct->id, 'additional_product_id' => $additionalProduct])->one();
                        if (empty($orderProductAdditionalProduct)) {
                            $orderProductAdditionalProduct = new OrderProductAdditionalProduct();
                            $orderProductAdditionalProduct->order_product_id = $orderProduct->id;
                            $orderProductAdditionalProduct->additional_product_id = $additionalProduct;
                            if ($orderProductAdditionalProduct->validate()) $orderProductAdditionalProduct->save();
                        }
                    }
                }
            } else die(var_dump($orderProduct->errors));
        } else throw new ForbiddenHttpException();
    }

    /**
     * @param int $orderId
     * @param int $productId
     * @param int $combinationId
     * @return OrderProduct
     */
    private function getOrderProductByCombinationId(int $orderId, int $productId, $combinationId)
    {
        $orderProduct = OrderProduct::findOne(['combination_id' => $combinationId,
            'order_id' => $orderId]);
        if (empty($orderProduct)) {
            $orderProduct = new OrderProduct();
            $orderProduct->product_id = $productId;
            $orderProduct->combination_id = $combinationId;
            $orderProduct->order_id = $orderId;
        }
        return $orderProduct;
    }

    /**
     * @param int $orderId
     * @param int $productId
     * @param int $priceId
     * @return OrderProduct
     */
    private function getOrderProductByPriceId(int $orderId, int $productId, $priceId)
    {
        $orderProduct = OrderProduct::findOne(['price_id' => $priceId, 'order_id' => $orderId]);
        if (empty($orderProduct)) {
            $orderProduct = new OrderProduct();
            $orderProduct->product_id = $productId;
            $orderProduct->price_id = $priceId;
            $orderProduct->order_id = $orderId;
        }

        return $orderProduct;
    }

    /**
     * Gets or creates incomplete order record from database.
     * @return array|Order|null|ActiveRecord
     */
    private function getIncompleteOrderFromDB()
    {
        $order = Order::find()->where([
            'user_id' => \Yii::$app->user->id,
            'status' => OrderStatus::STATUS_INCOMPLETE])
            ->one();

        if (empty($order)) {
            $order = new Order();
            $order->uid = $this->generateUniqueId($this->uidPrefix, $this->minOrderUid, $this->maxOrderUid);
            $order->user_id = \Yii::$app->user->id;
            $order->status = OrderStatus::STATUS_INCOMPLETE;
            if ($order->validate()) {
                $order->save();
            }
        }
        return $order;
    }

    /**
     * Saves product to session if user is guest or if the $saveToDataBase property is false.
     *
     * @param integer $productId
     * @param integer $count
     * @param integer $priceId
     * @param array|null $attributesAndValues
     * @param array|null $additionalProducts
     * @return boolean
     */
    private function saveProductToSession($productId, $count, $priceId = null, $attributesAndValues = null, $additionalProducts = null)
    {
        $additionalProductsTotalPrice = 0;
        if (!empty($additionalProducts)) {
            foreach ($additionalProducts as $additionalProduct) {
                $product = Product::findOne($additionalProduct);
                if (!empty($product)) $additionalProductsTotalPrice += $product->getPrice();
            }
        }

        if (!empty($productId) && (!empty($count))) {

            if ($this->enableGetPricesFromCombinations) {
                if (!empty($attributesAndValues)) {
                    $combination = $this->getCombination($attributesAndValues, $productId);
                    if (!empty($combination)) {
                        $price = $combination->salePrice;
                    } else return false;
                }
                else {
                    if (!empty($priceId)) $price = Price::findOne($priceId)->salePrice;
                    else $price = Product::findOne($productId)->getPrice();
                }
            } else {
                if (empty($priceId)) {
                    $product = Product::findOne($productId);
                    $price = $product->getPrice();
                } else {
                    $price = Price::findOne($priceId)->salePrice;
                }
            }

            $session = Yii::$app->session;

            $productsFromSession = $session[self::SESSION_KEY];
            if (!empty($productsFromSession)) {
                foreach ($productsFromSession as $key => $product) {
                    if ($product['id'] == $productId && (!empty($combination) || !empty($priceId)) &&
                        (
                            ($this->enableGetPricesFromCombinations && $product['combinationId'] == $combination->id) ||
                            ($this->enableGetPricesFromCombinations && !is_null($priceId) && $product['priceId'] == $priceId) ||
                            (!$this->enableGetPricesFromCombinations && !is_null($priceId) && $product['priceId'] == $priceId)
                        )
                    ) {
                        $productsFromSession[$key]['count'] += $count;
                        if (!empty($additionalProducts)) {
                            $productsFromSession[$key]['additionalProducts'] =
                                array_merge($productsFromSession[$key]['additionalProducts'], $additionalProducts);
                        }
                        break;
                    } else {
                        if (count($productsFromSession) - 1 == $key) {
                            $productsFromSession[] = [
                                'id' => $productId, 'count' => $count, 'priceId' => $priceId,
                                'combinationId' => (!empty($combination)) ? $combination->id : null,
                                'additionalProducts' => $additionalProducts
                            ];
                        }
                    }
                }
                $session[self::SESSION_KEY] = $productsFromSession;
            } else {
                $_SESSION[self::SESSION_KEY][] =
                    ['id' => $productId, 'count' => $count, 'priceId' => $priceId,
                        'combinationId' => (!empty($combination)) ? $combination->id : null,
                        'additionalProducts' => $additionalProducts
                    ];
            }
            if (!$session->has(self::TOTAL_COST_KEY)) {
                $session[self::TOTAL_COST_KEY] = 0;
            }
            $session[self::TOTAL_COST_KEY] += $price * $count;
            $session[self::TOTAL_COST_KEY] += $additionalProductsTotalPrice;
            return true;

        } else return false;

    }

    /**
     * @param $attributes
     * @param $productId
     * @return array|bool|null|ActiveRecord
     */
    public function getCombination($attributes, $productId)
    {
        $combinationIds = [];

        foreach ($attributes as $attribute) {
            $attribute = Json::decode($attribute);

            $productCombinationAttribute = CombinationAttribute::find()->asArray()
                ->select('combination_id')
                ->where(['attribute_id' => $attribute['attributeId'],
                    'attribute_value_id' => $attribute['valueId']])->all();

            if (empty($productCombinationAttribute)) return false;

            foreach ($productCombinationAttribute as $itemKey => $item) {
                $productCombinationAttribute[$itemKey] = $productCombinationAttribute[$itemKey]['combination_id'];
            }
            $combinationIds[] = $productCombinationAttribute;
        }

        if (count($combinationIds) > 1) {
            for ($i = 0; $i < count($combinationIds) - 1; $i++) {
                $combinationId = array_intersect($combinationIds[$i], $combinationIds[$i + 1]);
            }
        } else $combinationId = $combinationIds[0];
        if (!empty($combinationId)) {
            foreach ($combinationId as $item) {
                $combination = Combination::find()->where(['id' => $item, 'product_id' => $productId])->one();
                if (!empty($combination)) {
                    return $combination;
                }
            }
        }
        return false;
    }

    /**
     * Gets order items.
     *
     * @return array|bool|mixed|\yii\db\ActiveRecord[]
     */
    public function getOrderItems()
    {
        if (\Yii::$app->user->isGuest) {
            $session = \Yii::$app->session;
            $products = $session[self::SESSION_KEY];
        } else {

            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])->one();
            if (!empty($order)) {
                $products = OrderProduct::find()->asArray()->where(['order_id' => $order->id])->all();

            } else return false;

        }
        return $products;

    }

    /**
     * Gets order items count.
     *
     * @return integer
     */
    public function getOrderItemsCount()
    {
        if (\Yii::$app->user->isGuest) {
            $session = \Yii::$app->session;
            return count($session[self::SESSION_KEY]);
        } else {
            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])->one();
            if (!empty($order)) {
                $count = OrderProduct::find()->asArray()->where(['order_id' => $order->id])->count();

            } else $count = 0;

        }
        return $count;

    }

    /**
     * Gets all user orders from database.
     *
     * @return bool|\yii\db\ActiveRecord[]
     */
    public function getAllUserOrders()
    {
        if (!\Yii::$app->user->isGuest && $this->saveToDataBase === true) {
            $orders = Order::find()
                ->where(['user_id' => \Yii::$app->user->id])
                ->andWhere(['!=', 'status', OrderStatus::STATUS_INCOMPLETE])->all();
            return $orders;
        } else return false;
    }

    /**
     * Removes item from order.
     * @param $id
     */
    public function removeItem($id)
    {
        if (!\Yii::$app->user->isGuest) {
            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])->one();
            if (!empty($order)) {
                $orderProduct = OrderProduct::find()->where([
                    'product_id' => $id, 'order_id' => $order->id
                ])->one();
                if (!empty($orderProduct)) {
                    $orderProduct->delete();
                }
            }
        } else {
            $session = Yii::$app->session;
            if ($session->has(self::SESSION_KEY)) {
                $products = $session[self::SESSION_KEY];
                foreach ($products as $key => $product) {
                    if ($product['id'] == $id) {

                        if ($this->enableGetPricesFromCombinations) {
                            if (!empty($session[self::SESSION_KEY][$key]['combinationId'])) {
                                $combination = Combination::findOne($session[self::SESSION_KEY][$key]['combinationId']);
                                $price = $combination->salePrice;
                            }
                        } else {
                            if (!empty($session[self::SESSION_KEY][$key]['priceId'])) {
                                $price = Price::findOne($session[self::SESSION_KEY][$key]['priceId'])->salePrice;
                            } else $price = Product::findOne($id)->getPrice();
                        }
                        $session[self::TOTAL_COST_KEY] -= $price * $session[self::SESSION_KEY][$key]['count'];

                        unset($_SESSION[self::SESSION_KEY][$key]);
                    }
                }
            }
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function makeOrder()
    {
        $this->trigger(self::EVENT_BEFORE_GET_ORDER);

        if ($this->saveToDataBase === true) {
            if (!Yii::$app->user->isGuest) {
                return $this->makeOrderFromDB();
            } else return false;
        } else {
            return $this->makeOrderFromSession();
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function makeOrderFromDB()
    {
        $this->trigger(self::EVENT_BEFORE_GET_ORDER_FROM_DB,
            new OrderInfoEvent([
                'user_id' => \Yii::$app->user->id,
                'email' => \Yii::$app->user->identity->email])
        );
        $user = User::findOne(\Yii::$app->user->id);
        $order = Order::find()->where(['user_id' => $user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])->one();
        $profile = $order->userProfile;

        if ($profile->load(Yii::$app->request->post())) {
            if ($profile->validate()) {
                $profile->save();
            }
        }
        if ($order->load(Yii::$app->request->post())) {
            if (empty($order->address_id)) {
                $address = new UserAddress();
                if ($address->load(Yii::$app->request->post())) {
                    $address->user_profile_id = $user->id;
                    if ($address->validate()) {
                        if (!empty($address->city) && !empty($address->street) && !empty($address->house)) {
                            $address->save();
                            $order->address_id = $address->id;
                        }
                    }
                }
            } else $address = null;
            $order->user_id = $user->id;
            $order->status = OrderStatus::STATUS_CONFIRMED;
            $order->confirmation_time = new Expression('NOW()');
            $order->total_cost = $this->getTotalCost();

            if ($order->validate()) {
                $order->save();
                $this->sendMail($profile, $user, $order, $address, $order->address_id);
                return true;
            } else die(var_dump($order->errors));
        } else throw new Exception();
    }

    /**
     * @return bool
     */
    private function makeOrderFromSession()
    {
        $profile = new Profile();
        $user = new User();
        $order = new Order();
        $address = new UserAddress();

        if ($profile->load(Yii::$app->request->post()) &&
            $user->load(Yii::$app->request->post())
        ) {
            $order->load(Yii::$app->request->post());
            $address->load(Yii::$app->request->post());
            $this->sendMail($profile, $user, $order, $address);
            $this->clearCart();
            return true;
        } else return false;
    }

    /**
     * @param $profile
     * @param $user
     * @param $order
     * @param null $address
     * @param null $addressId
     * @throws Exception
     */
    private function sendMail($profile, $user, $order, $address = null, $addressId = null)
    {
        if (Yii::$app->user->isGuest) {
            $session = \Yii::$app->session;
            $productsArray = $session[self::SESSION_KEY];

            $ids = [];
            foreach ($productsArray as $item) {
                $ids[] = $item['id'];
            }

            $products = Product::find()->where(['in', 'id', $ids])->all();

            foreach ($products as $product) {
                foreach ($productsArray as $item) {
                    if ($item['id'] == $product->id) {
                        $product->count = $item['count'];
                        if (!empty($item['priceId'])) $product->price = Price::findOne($item['priceId'])->salePrice;
                    }
                }
            }
        } else {
            $products = OrderProduct::find()->where(['order_id' => $order->id])->all();
            if (!empty($addressId)) {
                $address = UserAddress::findOne($addressId);
            }
        }
        if (!empty($this->sender) && !empty($order)) {
            try {
                foreach ($this->sendTo as $admin) {
                    Yii::$app->shopMailer->compose('new-order',
                        ['products' => $products, 'user' => $user, 'profile' => $profile, 'order' => $order, 'address' => $address])
                        ->setFrom($this->sender)
                        ->setTo($admin)
                        ->setSubject(Yii::t('cart', 'New order'))
                        ->send();
                }

                Yii::$app->shopMailer->compose('order-success',
                    ['products' => $products, 'user' => $user, 'profile' => $profile, 'order' => $order, 'address' => $address])
                    ->setFrom($this->sender)
                    ->setTo($user->email)
                    ->setSubject(Yii::t('cart', 'Success order'))
                    ->send();
            } catch (Exception $ex) {
                throw new Exception($ex);
            }
        }
    }

    /**
     * Clears cart.
     */
    public function clearCart()
    {
        if (!\Yii::$app->user->isGuest && $this->saveToDataBase === true) {
            $order = Order::find()->where(['user_id' => \Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])->one();
            $order->deleteAll();
        } else {
            $session = \Yii::$app->session;
            $session->remove(self::SESSION_KEY);
            $session->remove(self::TOTAL_COST_KEY);
        }
    }

    /**
     * Moves order products from session to database if $saveToDataBase property is true.
     * @throws Exception
     */
    public function transportSessionDataToDB()
    {
        if ($this->saveToDataBase === true) {
            $session = Yii::$app->session;

            if ($session->has(self::SESSION_KEY)) {

                $order = $this->getIncompleteOrderFromDB();
                $products = $session[self::SESSION_KEY];

                foreach ($products as $product) {

                    if (\Yii::$app->cart->enableGetPricesFromCombinations && !empty($product['combinationId'])) {
                        $orderProduct = $this->getOrderProductByCombinationId($order->id, $product['id'], $product['combinationId']);
                    }
                    elseif ((\Yii::$app->cart->enableGetPricesFromCombinations && empty($product['combinationId'])) ||
                        (!\Yii::$app->cart->enableGetPricesFromCombinations && !empty($product['priceId']))) {
                        $orderProduct = $this->getOrderProductByPriceId($order->id, $product['id'], $product['priceId']);
                    }

                    if (!empty($orderProduct)) {
                        $orderProduct->count += $product['count'];
                        if ($orderProduct->validate()) {
                            $orderProduct->save();

                            if (!empty($product['additionalProducts'])) {
                                foreach ($product['additionalProducts'] as $productAdditionalProduct) {
                                    $additionalProduct = OrderProductAdditionalProduct::find()
                                        ->where(['order_product_id' => $orderProduct->id, 'additional_product_id' => $productAdditionalProduct])->one();

                                    if (empty($additionalProduct)) {
                                        $additionalProduct = new OrderProductAdditionalProduct();
                                        $additionalProduct->order_product_id = $orderProduct->id;
                                        $additionalProduct->additional_product_id = $productAdditionalProduct;
                                        if ($additionalProduct->validate()) $additionalProduct->save();
                                    }
                                }
                            }

                        } else throw new Exception($orderProduct->errors);
                    }
                }
            }
        }
    }

    /**
     * @return bool|int|mixed
     */
    public function getTotalCost()
    {
        if (Yii::$app->user->isGuest) {
            $session = Yii::$app->session;
            if ($session->has(self::TOTAL_COST_KEY)) {
                $totalCost = $session[self::TOTAL_COST_KEY];
                return $totalCost;
            } else return false;
        } else {
            $totalCost = 0;
            $order = Order::find()
                ->where(['user_id' => Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])
                ->one();
            if (!empty($order)) {
                $orderProducts = OrderProduct::find()->where(['order_id' => $order->id])->all();

                if (!empty($orderProducts)) {
                    foreach ($orderProducts as $product) {
                        if (\Yii::$app->cart->enableGetPricesFromCombinations) {
                            if (!empty($product->combination)) {
                                $totalCost += $product->count * $product->combination->salePrice;
                            } else {
                                $totalCost += $product->count * $product->getPrice();
                            }
                        } else {
                            $totalCost += $product->count * $product->getPrice();
                        }
                        if (!empty($product->orderProductAdditionalProduct)) {
                            foreach ($product->orderProductAdditionalProduct as $orderProductAdditionalProduct) {
                                $totalCost += $orderProductAdditionalProduct->additionalProduct->getPrice();
                            }
                        }
                    }
                }
            }
            return $totalCost;
        }
    }

    /**
     * @param $prefix
     * @param $min
     * @param $max
     * @return string
     */
    public function generateUniqueId($prefix, $min, $max)
    {
        $id = random_int($min, $max);
        $order = Order::find()->where(['uid' => $id])->one();
        if (empty($order)) {
            return $prefix . $id;
        } else $this->generateUniqueId($prefix, $min, $max);
    }


    /**
     * @param $id
     * @return boolean
     * Changes count of products in incomplete order in database.
     */
    public function changeOrderProductCountInDB($id)
    {
        if (!empty($id)) {
            if (!Yii::$app->user->isGuest) {
                $order = Order::find()
                    ->where(['user_id' => Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])->one();
                if (!empty($order)) {
                    $orderProduct = OrderProduct::find()
                        ->where(['product_id' => $id, 'order_id' => $order->id])->one();

                    if (!empty($orderProduct)) {
                        $orderProduct->count = Yii::$app->request->post('count');
                        $orderProduct->save();
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $id
     * @return bool
     * Changes count of products in order in session.
     */
    public function changeOrderProductCountInSession($id)
    {
        if (!empty($id)) {
            if (Yii::$app->user->isGuest) {
                $session = Yii::$app->session;
                if ($session->has(self::SESSION_KEY)) {
                    $products = $session[self::SESSION_KEY];
                    foreach ($products as $key => $product) {
                        if ($product['id'] == $id) {

                            if (!empty($session[self::SESSION_KEY][$key]['count'])) {
                                $_SESSION[self::SESSION_KEY][$key]['count'] = Yii::$app->request->post('count');
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
}