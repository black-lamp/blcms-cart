<?php
namespace bl\cms\cart\widgets;

use dektrium\user\models\RegistrationForm;
use yii\base\Widget;

/**
 * This widget is for Dektrium User module.
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 * (c) Dektrium project <http://github.com/dektrium>
 */
class Register extends Widget
{
    /**
     * @var bool
     */
    public $validate = true;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('register', [
            'model' => \Yii::createObject(RegistrationForm::className()),
        ]);
    }
}