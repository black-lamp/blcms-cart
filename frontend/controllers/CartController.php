<?php
namespace bl\cms\cart\frontend\controllers;

use bl\cms\seo\StaticPageBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use bl\imagable\helpers\FileHelper;
use yii\web\{Controller, NotFoundHttpException};
use bl\cms\shop\common\entities\{Product, ProductPrice};
use bl\cms\cart\common\components\user\models\{Profile, User, UserAddress};
use bl\cms\cart\models\{CartForm, DeliveryMethod, Order, OrderProduct, OrderStatus};

/**
 * @author Albert Gainutdinov
 */
class CartController extends Controller
{

    public function behaviors()
    {
        return [
            'staticPage' => [
                'class' => StaticPageBehavior::className(),
                'key' => 'cart'
            ]
        ];
    }

    public $defaultAction = 'show';

    public function actionAdd()
    {
        $model = new CartForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                Yii::$app->cart->add($model->productId, $model->count, $model->priceId);
                \Yii::$app->getSession()->setFlash('success', Yii::t('shop', 'You have successfully added this product to cart'));
            } else die(var_dump($model->errors));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionShow()
    {
        $cart = \Yii::$app->cart;
        $items = $cart->getOrderItems();

        $this->registerStaticSeoData();

        /*EMPTY CART*/
        if (empty($items)) {
            return $this->render('empty-cart');
        }

        /*CART IS NOT EMPTY*/
        else {
            /*FOR GUEST*/
            if (\Yii::$app->user->isGuest) {

                $order = new Order();
                $user = new User();
                $profile = new Profile();
                $address = new UserAddress();

                $products = Product::find()->where(['in', 'id', ArrayHelper::getColumn($items, 'id')])->all();

                foreach ($products as $product) {
                    foreach ($items as $item) {
                        if ($item['id'] == $product->id) {
                            $product->count = $item['count'];
                            $product->price = (!empty($item['priceId'])) ? ProductPrice::findOne($item['priceId'])->salePrice : $product->price;
                        }
                    }
                }

                return $this->render('show-for-guest', [
                    'products' => $products,
                    'order' => $order,
                    'profile' => $profile,
                    'user' => $user,
                    'address' => $address,
                ]);
            }
            /*FOR USER*/
            else {
                $order = Order::find()
                    ->where(['user_id' => \Yii::$app->user->id, 'status' => OrderStatus::STATUS_INCOMPLETE])
                    ->one();
                if (!empty($order)) {
                    $orderProducts = OrderProduct::find()->where(['order_id' => $order->id])->all();

                    $profile = Profile::find()->where(['user_id' => \Yii::$app->user->id])->one();

                    return $this->render('show', [
                        'order' => new Order(),
                        'profile' => $profile,
                        'user' => new User,
                        'address' => new UserAddress(),
                        'productsFromDB' => $orderProducts,
                    ]);
                }
            }
        }
    }

    public function actionRemove($id)
    {
        \Yii::$app->cart->removeItem($id);
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionClear()
    {
        \Yii::$app->cart->clearCart();
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionMakeOrder()
    {
        if (Yii::$app->request->isPost) {

            if(\Yii::$app->cart->makeOrder()) {
                \Yii::$app->session->setFlash('success', \Yii::t('shop', 'Your order is accepted. Thank you.'));

                return $this->render('order-success');
            }
            else {
                \Yii::$app->session->setFlash('error', \Yii::t('shop', 'Making order error'));
                return $this->render('order-error');
            }
        }
        else {
            throw new NotFoundHttpException();
        }
    }

    public function actionGetDeliveryMethod($id)
    {
        if (\Yii::$app->request->isAjax) {

            $method = DeliveryMethod::findOne($id);
            $methodTranslation = $method->translation;

            $method = ArrayHelper::toArray($method);
            $method['translation'] = ArrayHelper::toArray($methodTranslation);
            $method['image_name'] = '/images/delivery/' .
                FileHelper::getFullName(
                    \Yii::$app->shop_imagable->get('delivery', 'small', $method['image_name']
                    ));
            return json_encode([
                'method' => $method,
                'field' => '<input type="text" id="useraddress-zipcode" class="form-control" name="UserAddress[zipcode]">'
            ]);
        }
        else throw new NotFoundHttpException();
    }


}