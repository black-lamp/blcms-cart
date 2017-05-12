<?php
namespace bl\cms\cart\frontend\controllers;

use bl\cms\cart\models\Order;
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

            return $this->render('order-list', [
                'userOrders' => $userOrders
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
}