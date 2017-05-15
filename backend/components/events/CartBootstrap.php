<?php
namespace bl\cms\cart\backend\components\events;

use bl\cms\cart\backend\controllers\OrderController;
use bl\cms\cart\backend\events\OrderEvent;
use bl\emailTemplates\data\Template;
use bl\multilang\entities\Language;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\bootstrap\Html;
use yii\db\Exception;
use yii\helpers\Url;

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
        if ($event->sender->module->enableLog) {

            $userId = \Yii::$app->user->id;
            $order = $event->model;

            $message = "ID: $order->id userId: $userId";

            Yii::info($message, $event->name);
        }
    }

    /**
     * @param OrderEvent $event
     * @throws \yii\base\Exception
     *
     * Sends email to customer
     */
    public function send($event)
    {
        $mail = $event->model->orderStatus->mail;

        if (!empty($mail)) {
            try {

                /**
                 * @var $mailTemplate Template
                 */
                $mailTemplate = Yii::$app->get('emailTemplates')->getTemplate($mail->key, Language::getCurrent()->id);
                $vars = [
                    '{order_id}' => $event->model->uid,
                    '{created_at}' => $event->model->creation_time,
                    '{status}' => $event->model->orderStatus->translation->title,
                    '{order_invoice}' => $event->model->invoice,
                    '{order_pay_btn}' => $event->model->invoice
                        ? Html::tag('p', Html::a(Yii::t('order.mail', 'Pay'), $event->model->invoice, [
                            'target' => '_blank',
                            'style' => [
                                'background-color' => '#f09020',
                                'color' => '#ffffff',
                                'font-size' => '20px',
                                'padding' => '7px 13px',
                                'text-decoration' => 'none',
                                'border-radius' => '4px'
                            ]
                        ]), [
                            'style' => [
                                'text-align' => 'center'
                            ]
                        ])
                        : ''
                ];
                $mailTemplate->parseSubject($vars);
                $mailTemplate->parseBody($vars);

                Yii::$app->shopMailer->compose('mail-body',
                    ['bodyContent' => $mailTemplate->getBody()])
                    ->setFrom([\Yii::$app->cart->sender ?? \Yii::$app->shopMailer->transport->getUsername() => \Yii::$app->name ?? Url::to(['/'], true)])
                    ->setTo($event->model->user->email)
                    ->setSubject($mailTemplate->getSubject())
                    ->send();

            } catch (Exception $ex) {
                throw new Exception($ex);
            }
        }
    }

}