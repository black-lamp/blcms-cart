<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $model bl\cms\cart\models\OrderStatus
 * @var $modelTranslation bl\cms\cart\models\OrderStatusTranslation
 * @var $selectedLanguage \bl\multilang\entities\Language
 * @var $languages \bl\multilang\entities\Language[]
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use bl\cms\shop\widgets\LanguageSwitcher;

$this->title = ($modelTranslation->isNewRecord) ?
    Yii::t('cart', 'Creating new order status') :
    Yii::t('cart', 'Editing order status');
?>
<div class="panel panel-default">
    <div class="panel-heading">

        <h1><?= Html::encode($this->title) ?></h1>

        <!-- LANGUAGES -->
        <?= LanguageSwitcher::widget([
            'languages' => $languages,
            'selectedLanguage' => $selectedLanguage,
            'model' => $model
        ]); ?>
    </div>

    <div class="panel-body">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($modelTranslation, 'title')->textInput(['maxlength' => true]) ?>

        <div class="row">
            <?= Html::a(Yii::t('shop', 'Cancel'), Url::toRoute('/cart/order-status'), ['class' => 'm-r-xs btn btn-danger btn-xs pull-right']); ?>
            <?= Html::submitButton(Yii::t('shop', 'Save'), ['class' => 'btn btn-primary btn-xs m-r-xs pull-right']); ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>