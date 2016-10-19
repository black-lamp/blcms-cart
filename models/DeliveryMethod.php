<?php
namespace bl\cms\cart\models;

use bl\imagable\helpers\FileHelper;
use bl\multilang\behaviors\TranslationBehavior;
use Exception;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\BaseFileHelper;

/**
 * This is the model class for table "shop_delivery_method".
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property string $image_name
 *
 * @property DeliveryMethodTranslation[] $deliveryTranslations
 * @property Order[] $orders
 */
class DeliveryMethod extends ActiveRecord
{

    public $logo;

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
            [['logo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['image_name'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'logo' => Yii::t('shop', 'Logo'),
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

    /**
     * @return array
     * @throws Exception
     */
    public function upload()
    {
        if ($this->validate()) {
            $imagable = \Yii::$app->shop_imagable;

            if (!empty($this->logo)) {
                if (!file_exists($imagable->imagesPath)) BaseFileHelper::createDirectory($imagable->imagesPath);
                $newFile = $imagable->imagesPath . $this->logo->name;

                if ($this->logo->saveAs($newFile)) {
                    $image_name = $imagable->create('delivery', $newFile);

                    unlink($newFile);
                    return $image_name;
                }

            }
        }
        else throw new Exception('Image saving failed.');
    }

    public function getBigLogo() {
        $logo = $this->getLogo('big');
        return '/images/delivery/' . FileHelper::getFullName($logo);
    }

    public function getThumbLogo() {
        $logo = $this->getLogo('thumb');
        return '/images/delivery/' . FileHelper::getFullName($logo);
    }

    public function getSmallLogo() {
        $logo = $this->getLogo('small');
        return '/images/delivery/' . FileHelper::getFullName($logo);
    }

    private function getLogo($size) {
        if (!empty($this->image_name)) {
            return \Yii::$app->shop_imagable->get('delivery', $size, $this->image_name);
        }
    }
}