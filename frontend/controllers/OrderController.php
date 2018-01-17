<?php
namespace bl\cms\cart\frontend\controllers;

use bl\cms\cart\models\Order;
use bl\cms\cart\models\OrderSearch;
use bl\cms\cart\widgets\OrderSum;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class OrderController extends Controller
{

    /**
     * Return list of user orders.
     *
     * @return array Order
     * @throws NotFoundHttpException
     */
    public function actionShowOrderList() {
        if (!\Yii::$app->user->isGuest) {
            $userOrders = \Yii::$app->cart->getAllUserOrders();
            $filterModel = new OrderSearch();
            $dataProvider = $filterModel->search(Yii::$app->request->get());

            return $this->render('order-list', [
                'userOrders' => $userOrders,
                'filterModel' => $filterModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        else throw new NotFoundHttpException();
    }

    /**
     * @param $id integer
     * @return Order
     * @throws NotFoundHttpException
     */
    public function actionView($id) {
        if (!empty($id)) {
            $order = Order::findOne($id);
            if ($order->user_id == \Yii::$app->user->id) {
                return $this->render('view', [
                    'order' => $order
                ]);
            }
        }
        throw new NotFoundHttpException();
    }

    public function actionSum() {
        if(\Yii::$app->request->isAjax) {
            return OrderSum::widget();
        }

        throw new NotFoundHttpException();
    }

    public function actionRepeat($id) {
        $order = Order::findOne($id);

        if(empty($order)) {
            Yii::$app->session->setFlash('error', Yii::t('order', 'Wrong order id.'));
            return $this->goBack();
        }

        Yii::$app->cart->clearCart();
        foreach ($order->orderProducts as $orderProduct) {
            Yii::$app->cart->add($orderProduct->product_id, $orderProduct->count, null, [], $orderProduct->combination_id);
        }

        return $this->redirect(['/cart']);
    }
}