<?php
namespace bl\cms\cart\frontend\components\events;

use bl\cms\cart\frontend\components\user\controllers\RegistrationController;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class UserRegistrationBootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        Event::on(RegistrationController::className(),
            RegistrationController::EVENT_AFTER_REGISTER, [$this, 'addLogRecord']);
        Event::on(RegistrationController::className(),
            RegistrationController::EVENT_AFTER_CONFIRM, [$this, 'addLogRecord']);
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
            $message = "UserId: $userId";

            Yii::info($message, $event->name);
        }
    }
}