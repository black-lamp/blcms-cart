<?php
namespace bl\cms\cart\models;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_delivery_method".
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 *
 * @property DeliveryMethodTranslation[] $deliveryTranslations
 * @property Order[] $orders
 */
class DeliveryMethod extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_delivery_method';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => DeliveryMethodTranslation::className(),
                'relationColumn' => 'delivery_method_id'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'post_office' => Yii::t('shop', 'Post office'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopDeliveryTranslations()
    {
        return $this->hasMany(DeliveryMethodTranslation::className(), ['delivery_method_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopOrders()
    {
        return $this->hasMany(Order::className(), ['delivery_method' => 'id']);
    }
}