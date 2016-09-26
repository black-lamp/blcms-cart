<?php
namespace bl\cms\cart;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_order".
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property integer $status
 *
 * @property OrderProduct[] $orderProducts
 */

class Order extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'phone', 'user_id'], 'required'],
            [['status', 'user_id'], 'integer'],
            [['first_name', 'last_name', 'email', 'phone', 'address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('shop', 'ID'),
            'first_name' => Yii::t('shop', 'First Name'),
            'last_name' => Yii::t('shop', 'Last Name'),
            'email' => Yii::t('shop', 'Email'),
            'phone' => Yii::t('shop', 'Phone'),
            'address' => Yii::t('shop', 'Address'),
            'status' => Yii::t('shop', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProducts()
    {
        return $this->hasMany(OrderProduct::className(), ['order_id' => 'id']);
    }
}