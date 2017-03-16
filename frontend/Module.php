<?php
namespace bl\cms\cart\frontend;

use bl\cms\cart\CartComponent;
use Yii;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'bl\cms\cart\frontend\controllers';
    public $defaultRoute = 'cart';

    /**
     * @var CartComponent
     */
    public static $cart;

    /**
     * @var bool
     * Enables logging
     */
    public $enableLog = false;

    public function init()
    {
        parent::init();

        self::$cart = Yii::$app->cart;

        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['cart'] =
            Yii::$app->i18n->translations['cart'] ?? [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath'       => '@vendor/black-lamp/blcms-cart/frontend/messages',
        ];
    }
}
