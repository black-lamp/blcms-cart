<?php
namespace bl\cms\cart\widgets\assets;

use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class OrderCounterAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-cart/widgets/assets/src';

    public $css = [
        'css/order-counter.css'
    ];
    public $js = [
    ];
    public $depends = [
    ];
}