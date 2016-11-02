<?php
namespace bl\cms\cart\widgets;

use bl\cms\cart\common\components\user\models\Profile;
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
            'profile' => \Yii::createObject(Profile::className()),
            'model' => \Yii::createObject(RegistrationForm::className()),
        ]);
    }
}