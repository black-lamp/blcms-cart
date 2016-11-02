Blcms-cart
=====================
Cart module and component for Blcms-shop module


Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run
```
php composer.phar require --prefer-dist black-lamp/blcms-cart "*"
```
or add to the require section of your `composer.json` file:
```
"black-lamp/blcms-cart": "*"
```


If you need to save orders to database, apply next migration:

**Migrations**
php yii migrate --migrationPath=@vendor/black-lamp/blcms-cart/migrations

**Common configuration**
```
'components' => [
    'cart' => [
                'class' => bl\cms\cart\CartComponent::className(),
                'emailNotifications' => true,
                'sender' => 'info@mail.com',
                'sendTo' => [
                    'test@mail.com'
                ],
                'saveToDataBase' => true,
                
                'newOrderMailView' => ['@frontend/views/new-order'], //The path of e-mail view which will be sent to admins after success order.
                'orderSuccessView' => ['@frontend/views/order-success'],
            ],
],
```

**Frontend configuration**
```
'modules' => [
    'cart' => [
                'class' => bl\cms\cart\frontend\Module::className(),
            ],
    'user' => [
                'class' => 'dektrium\user\Module',
                'modelMap' => [
                    'User' => 'bl\cms\cart\common\components\user\models\User',
                    'Profile' => 'bl\cms\cart\common\components\user\models\Profile',
                ],
                'controllerMap' => [
                    'registration' => 'bl\cms\cart\common\components\user\controllers\RegistrationController',
                    'settings' => 'bl\cms\cart\frontend\components\user\controllers\SettingsController',
                ],
                'as frontend' => 'dektrium\user\filters\FrontendFilter',
            ],
'components' => [
            'shopMailer' => [
                'useFileTransport' => true,
                'class' => 'yii\swiftmailer\Mailer',
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'username' => '',
                    'password' => '',
                    'host' => 'smtp.gmail.com',
                    'port' => '587',
                    'encryption' => 'tls',
                ],
            ],
]
```

**Backend configuration**
```
'modules' => [
        'cart' => [
            'class' => bl\cms\cart\backend\Module::className(),
        ],
        'user' => [
                    'class' => 'dektrium\user\Module',
                    'enableRegistration' => false,
                    'enableConfirmation' => false,
                    'admins' => ['admin'],
                    'adminPermission' => 'rbacManager',
                    'modelMap' => [
                        'Profile' => 'bl\cms\cart\common\components\user\models\Profile',
                    ],
                    'as backend' => [
                        'class' => 'dektrium\user\filters\BackendFilter',
                        'only' => ['register'], // Block View Register Backend
                    ],
                ]
    ],
```

Usage
-----
Adding to cart
```
Yii::$app->cart->add($productId, $count, $priceId);
```

Getting total cost
```
$totalCost = Yii::$app->cart->getTotalCost();
```

Getting all user orders
'''
$orders = Yii::$app->cart->getAllUserOrders();
'''

Clearing cart
```
Yii::$app->cart->clearCart();
```

