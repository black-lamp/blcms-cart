<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $cost integer
 * @var $totalCost integer
 */
?>

<?php if (!empty($cost) && $cost != $totalCost) : ?>
    <p>
        <?= Yii::t('order', 'Sum') ?>
        <strike><?= $cost ?></strike>
    </p>
<?php endif; ?>
<p>
    <?= Yii::t('order', 'To pay -') ?>
    <?= $totalCost ?>
</p>