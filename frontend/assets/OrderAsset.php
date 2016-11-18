<?php

namespace bl\cms\cart\frontend\assets;

use yii\web\AssetBundle;

/**
 * Asset for OrderController actions
 */
class OrderAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-cart/frontend/web';

    public $css = [
        'css/order.css',
    ];
    public $js = [
    ];
    public $depends = [
    ];
}
