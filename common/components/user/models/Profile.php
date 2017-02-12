<?php
namespace bl\cms\cart\common\components\user\models;

use bl\cms\shop\common\components\user\models\User;
use dektrium\user\traits\ModuleTrait;
use dektrium\user\models\Profile as BaseProfile;
use Yii;

/**
 * This file overrides standart model of the Dektrium project Yii2-user.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $surname
 * @property string $patronymic
 * @property string $avatar
 * @property integer $phone
 * @property string $info
 *
 * @property User $user
 * @property UserAddress[] $userAddresses
 */


class Profile extends BaseProfile
{
    use ModuleTrait;
    /** @var \dektrium\user\Module */
    protected $module;
    /** @inheritdoc */
    public function init()
    {
        $this->module = \Yii::$app->getModule('user');
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['phone'], 'string', 'max' => 25],
            [['info'], 'string', 'max' => 120],
            [['name', 'surname', 'phone'], 'required'],
            [['name', 'surname', 'patronymic', 'avatar'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('cart', 'Name'),
            'surname' => Yii::t('cart', 'Surname'),
            'patronymic' => Yii::t('cart', 'Patronymic'),
            'avatar' => Yii::t('cart', 'Avatar'),
            'phone' => Yii::t('cart', 'Phone number'),
            'info' => Yii::t('cart', 'Information'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAddresses()
    {
        return $this->hasMany(UserAddress::className(), ['user_profile_id' => 'id']);
    }

    /**
     * @return string
     *
     * Gets current user name with surname as string.
     */
    public function getUserNameWithSurname():string {

        $string = $this->name . ' ' . $this->surname;
        return $string;
    }

}