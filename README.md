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
- php yii migrate --migrationPath=@vendor/black-lamp/blcms-staticpage/migrations
- php yii migrate --migrationPath=@vendor/black-lamp/blcms-cart/migrations

**Common configuration**
```
'name' => 'ProjectName',
'components' => [
    'cart' => [
                'class' => bl\cms\cart\CartComponent::className(),
                'emailNotifications' => true,
                'sender' => 'info@mail.com',
                'sendTo' => [
                    'test@mail.com'
                ],
                'saveToDataBase' => true,
                
                'enablePayment' => true, //If true, you need to add blcms-payment module to your composer.json file.
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
                    'User' => 'bl\cms\shop\common\components\user\models\User',
                    'Profile' => 'bl\cms\cart\common\components\user\models\Profile',
                ],
                'controllerMap' => [
                    'registration' => 'bl\cms\cart\common\components\user\controllers\RegistrationController',
                    'settings' => 'bl\cms\cart\frontend\components\user\controllers\SettingsController',
                ],
                'as frontend' => 'dektrium\user\filters\FrontendFilter',
            ],
'components' => [
    //For Dektrium User module
    'mailer' => [
                'class' => yii\swiftmailer\Mailer::className(),
                'useFileTransport' => false,
                'messageConfig' => [
                    'charset' => 'UTF-8',
                ],
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'username' => 'info@mail.com',
                    'password' => '123456789',
                    'host' => 'mail.server.com',
                    'port' => '587',
                ],
            ],
    //For cart
   'shopMailer' => [
        'class' => yii\swiftmailer\Mailer::className(),
        'useFileTransport' => false,
        'messageConfig' => [
            'charset' => 'UTF-8',
        ],
        'viewPath' => '@vendor/black-lamp/blcms-cart/frontend/views/mail',
        'htmlLayout' => '@vendor/black-lamp/blcms-cart/frontend/views/mail/layout',
        'transport' => [
                'class' => 'Swift_SmtpTransport',
                'username' => 'info@host.ua',
                'password' => 'password',
                'host' => 'pop.host.ua',
                'port' => '587',
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
                ],
        
        'seo' => [
            'class' => 'bl\cms\seo\backend\Module'
        ],
    ],
```

**Params**
'adminEmail' => 'info@mail.ua',

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

**Static page**

There is page in Static page module for cart show page on frontend. It has key 'cart'. 
You need to configure it: add title, seo-data etc.
http://YOUR_DOMAIN.com/admin/seo/static


**Logging**
This configuration is for Shop module and Cart module.

For enable logging add log component to your common configuration file:

```
'components' => [
        'log' => [
            'targets' => [
                [
                    'logTable' => 'shop_log',
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info'],
                    'logVars' => [],
                    'categories' => [
                        'afterCreateProduct', 'afterDeleteProduct', 'afterEditProduct',
                        'afterCreateCategory', 'afterEditCategory', 'afterDeleteCategory',
                    ],
                ],
                [
                    'logTable' => 'cart_log',
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info'],
                    'logVars' => [],
                    'categories' => [
                        'afterChangeOrderStatus'
                    ],
                ],
                [
                    'logTable' => 'user_log',
                    'class' => 'yii\log\DbTarget',
                    'logVars' => [],
                    'levels' => ['info'],
                    'categories' => [
                        'afterRegister', 'afterConfirm'
                    ],
                ],
            ],
        ],
```

Then apply migration, but only after you will configure your app.
The migration will create tables for log targets, which are listed in configuration.

```
php yii migrate --migrationPath=@yii/log/migrations/
```

In backend configuration of your module add
```
'enableLog' => true,
```


###Email templates
Configure frontend component:
```
components => [
    'emailTemplates' => [
        'class' => bl\emailTemplates\components\TemplateManager::class
    ],
]
```
After you will apply migrations, there will be able two records for emails - 'new-order' and 'order-success'.
You can find its in admin panel: /admin/email-templates/default/list
In mail subject and object you can use next variables:
{name}, {surname}, {patronymic}, {email}, {phone}, {orderUid}, {zip}, {country}, {region}, 
{city}, {street}, {house}, {apartment}, {products}, {totalCost}.
Variable {products} renders view @bl\cms\cart\frontend\views\mail\products
You can setup mail layout in frontend config for shopMailer component. Now it use @bl\cms\cart\frontend\views\mail\layout

**Welcome email**
You can create templates here: /admin/email-templates/default/list (use 'welcome' key for it)
Use next variables:
{token} => confirmation token

**Recovery email**
Create 'recovery' template.
Use next variables:
{token} => confirmation token
