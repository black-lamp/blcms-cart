<?php
namespace bl\cms\cart;

use bl\multilang\entities\Language;
use Exception;
use yii\base\Component;
use yii\helpers\Url;


/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Mailer extends Component
{
    /**
     * @param array $orderResult
     */
    public function sendMakeOrderMessage(array $orderResult)
    {
        $mailVars = [
            '{name}' => $orderResult['profile']->name,
            '{surname}' => $orderResult['profile']->surname,
            '{patronymic}' => $orderResult['profile']->patronymic,
            '{info}' => $orderResult['profile']->info,
            '{email}' => (!empty($orderResult['user']->identity)) ? $orderResult['user']->identity->email : $orderResult['user']->email,
            '{phone}' => $orderResult['profile']->phone,
            '{orderUid}' => $orderResult['order']->uid,
            '{products}' => \Yii::$app->view->render('@bl/cms/cart/frontend/views/mail/products', [
                'products' => $orderResult['order']->orderProducts
            ]),
            '{delivery}' => \Yii::$app->view->render('@bl/cms/cart/frontend/views/mail/delivery', [
                'order' => $orderResult['order'],
                'address' => $orderResult['address'],
            ]),
            '{payment}' => \Yii::$app->view->render('@bl/cms/cart/frontend/views/mail/payment', [
                'order' => $orderResult['order'],
            ]),
            '{totalCost}' => \Yii::$app->formatter->asCurrency($orderResult['order']->total_cost)
        ];

        $mailTemplate = $this->createMailTemplate('new-order', $mailVars);
        $subject = $mailTemplate->getSubject();
        $bodyParams = ['bodyContent' => $mailTemplate->getBody()];

        //Send to admins
        if (!empty(\Yii::$app->cart->sendTo)) {
            foreach (\Yii::$app->cart->sendTo as $adminMail) {
                $this->sendMessage(
                    $adminMail,
                    $subject,
                    $bodyParams);
            }
        }

        //Send to user
        $mailTemplate = $this->createMailTemplate('order-success', $mailVars);
        $subject = $mailTemplate->getSubject();
        $bodyParams = ['bodyContent' => $mailTemplate->getBody()];
        $this->sendMessage(
            (!empty($orderResult['user']->identity)) ? $orderResult['user']->identity->email : $orderResult['user']->email,
            $subject, $bodyParams);
    }

    /**
     * @param string $mailKey
     * @param array $mailVars
     * @return mixed
     */
    private function createMailTemplate(string $mailKey, array $mailVars) {

        $mailTemplate = \Yii::$app->get('emailTemplates')
            ->getTemplate($mailKey, Language::getCurrent()->id);
        $mailTemplate->parseSubject($mailVars);
        $mailTemplate->parseBody($mailVars);

        return $mailTemplate;
    }

    /**
     * @param $sendTo
     * @param $bodySubject
     * @param array $bodyParams
     * @throws Exception
     */
    protected function sendMessage($sendTo, $bodySubject, $bodyParams = [])
    {
        if (!empty($sendTo)) {
            try {

                \Yii::$app->shopMailer->compose('mail-body', $bodyParams)
                    ->setFrom([\Yii::$app->cart->sender => \Yii::$app->name ?? Url::to(['/'], true)])
                    ->setTo($sendTo)
                    ->setSubject($bodySubject)
                    ->send();

            } catch (Exception $ex) {
                throw new Exception($ex);
            }
        }
    }
}