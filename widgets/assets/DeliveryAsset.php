<?php

namespace bl\cms\cart\widgets\assets;

use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class DeliveryAsset extends AssetBundle
{

    public $sourcePath = '@vendor/black-lamp/blcms-cart/widgets/assets/src';

    public $css = [
        'css/delivery.css'
    ];
    public $js = [
        'js/delivery.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
