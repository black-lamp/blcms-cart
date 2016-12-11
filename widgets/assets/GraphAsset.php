<?php
namespace bl\cms\cart\widgets\assets;

use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class GraphAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-cart/widgets/assets/src';

    public $css = [
    ];
    public $js = [
        'js/graph.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}