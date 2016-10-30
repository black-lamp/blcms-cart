<?php
namespace bl\cms\cart\backend;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'bl\cms\cart\backend\controllers';
    public $defaultRoute = 'cart';

    public function init()
    {
        parent::init();
    }

}