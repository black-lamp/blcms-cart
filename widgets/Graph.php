<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * A points on y-axis for graph drawing
 * Ex.: \bl\cms\cart\widgets\Graph::widget([
 * 'graphPoints' => [
 *      ['Jan', 240],
 *      ['Feb', 120],
 *      ['Mar', 180],
 *      ['Apr', 900],
 *      ['May', 120],
 *  ]
 * ]);
 */

namespace bl\cms\cart\widgets;

use yii\base\Widget;

class Graph extends Widget
{
    /**
     * @var array
     */
    public $graphPoints;

    public function init()
    {
    }

    public function run()
    {
        return $this->render('graph', [
            'graphPoints' => $this->graphPoints
        ]);
    }
}