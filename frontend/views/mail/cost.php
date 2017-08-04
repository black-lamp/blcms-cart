<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $cost integer
 */
?>

<?php if (!empty($cost)) : ?>
    <p>
        <?= Yii::t('order', 'Sum') ?>
        <strike><?= $cost ?></strike>
    </p>
<?php endif; ?>
