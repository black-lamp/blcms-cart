<?php
namespace bl\cms\cart\backend\components\events;

use bl\cms\cart\backend\controllers\OrderController;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\Exception;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CartBootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        Event::on(OrderController::className(),
            OrderController::EVENT_AFTER_CHANGE_ORDER_STATUS, [$this, 'addLogRecord']);
        Event::on(OrderController::className(),
            OrderController::EVENT_AFTER_CHANGE_ORDER_STATUS, [$this, 'send']);
    }

    /**
     * @param $event
     *
     * Records log
     */
    public function addLogRecord($event) {
        /**
         * If logging is enabled
         */
        if ($event->sender->module->enableLogOnBackend) {

            $userId = \Yii::$app->user->id;
            $order = $event->model;

            $message = "ID: $order->id userId: $userId";

            Yii::info($message, $event->name);
        }
    }

    /**
     * @param $event
     * @throws \yii\base\Exception
     *
     * Sends email to customer
     */
    public function send($event)
    {
        try {
            Yii::$app->shopMailer->compose('change-order-status',
                ['model' => $event->model])
                ->setFrom(Yii::$app->cart->sender)
                ->setTo($event->model->user->email)
                ->setSubject(Yii::t('cart', 'Your order') . ' #' . $event->model->uid . Yii::t('cart', ' is ') .
                    mb_strtolower($event->model->orderStatus->translation->title))
                ->send();

        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }

}