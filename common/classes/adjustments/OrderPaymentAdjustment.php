<?php
namespace bl\cms\cart\common\classes\adjustments;

use bl\cms\cart\common\classes\OrderPaymentDiscountCounter;
use bl\cms\cart\models\Order;
use bl\cms\payment\common\entities\PaymentMethod;
use ReflectionClass;
use ReflectionMethod;
use Yii;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class OrderPaymentAdjustment extends DiscountAdjustment
{
    /**
     * @param $sum
     * @return int
     */
    function countAdjustment($sum)
    {
        $this->percent = 0;

        $order = new Order();

        if(Yii::$app->request->isGet) {
            $params = Yii::$app->request->get();
        }
        else if(Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
        }
        else {
            return 0;
        }

        if ($order->load($params)) {
            if(!empty($order->payment_method_id)) {
                $paymentMethod = PaymentMethod::findOne($order->payment_method_id);
                if(!empty($paymentMethod)) {
                    if(!empty($paymentMethod->discount_counter)) {
                        $counterClass = new ReflectionClass($paymentMethod->discount_counter);
                        if($counterClass->isSubclassOf(OrderPaymentDiscountCounter::className())) {
                            $counterInstance = $counterClass->newInstanceArgs();
                            if($counterInstance->load(Yii::$app->request->isGet
                                ? \Yii::$app->request->get()
                                :  \Yii::$app->request->post())) {

                                if($counterInstance->validate()) {
                                    $this->percent = $counterInstance->getDiscount();
                                }
                                else {
                                    /*die(var_dump($counterInstance->getErrors()));*/
                                }
                            }

                            /*$counterMethod = new ReflectionMethod($paymentMethod->discount_counter, 'getDiscount');
                            $counterResult = $counterMethod->invoke(null);

                            if(!empty($counterResult)) {
                                return $counterResult;
                            }*/
                        }
                    }
                    else if(!empty($paymentMethod->discount)) {
                        $this->percent = $paymentMethod->discount;
                    }
                }
            }
        }

        return parent::countAdjustment($sum);
    }
}