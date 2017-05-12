<?php
namespace bl\cms\cart\frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class OrderSumAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-cart/frontend/web';
    public $basePath = '@vendor/black-lamp/blcms-cart/frontend/web';

    public $css = [
    ];
    public $js = [
        'js/order-sum.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
