black-lamp/blcms-cart
=====================
Cart component for Blcms-shop module

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist black-lamp/blcms-cart "*"
```

or add

```
"black-lamp/blcms-cart": "*"
```

to the require section of your `composer.json` file.

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


Usage
-----