# yii2-cart
Cart component for Blcms-shop module

If you need to save orders to database, apply next migration and setup Blcms-shop module in frontend config file:

**Migrations**
php yii migrate --migrationPath=@vendor/black-lamp/blcms-cart/migrations

**Frontend configuration**
```
'bootstrap' => [
        'shop'
    ],
'modules' => [
    'shop' => [
            'class' => 'bl\cms\shop\frontend\Module',
            'cartConfig' => [
                'emailNotifications' => true,
                'sendTo' => [
                    'guts.vadim@gmail.com',
                    'xalbert.einsteinx@gmail.com'
                ],
                'saveToDataBase' => true
            ]
        ]
    ]
```