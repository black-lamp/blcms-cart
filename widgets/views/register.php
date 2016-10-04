<?php

/**
 * This view is for widget for Dektrium User module.
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 * (c) Dektrium project <http://github.com/dektrium>
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $user
 */

?>
<?php $form = ActiveForm::begin([
    'action' => '/user/registration/register',
    'id' => 'registration-form',
]); ?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="panel-body">
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($profile, 'name') ?>
                <?= $form->field($profile, 'surname') ?>
                <?= $form->field($profile, 'phone') ?>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <?= Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'btn btn-success']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>