# Generators for Gii module of Yii2 Framework

[![Total Downloads](https://img.shields.io/packagist/dt/sorokinmedia/yii2-generators.svg)](https://packagist.org/packages/sorokinmedia/yii2-generators)

The extension allows to generate some types of classes on base of existing `ActiveRecord` model class. 

## Installation

Install with composer:

```bash
composer require --dev ma3obblu/yii2-generators
```

or add

```bash
"ma3obblu/yii2-generators": "*"
```

to the require section of your `composer.json` file.

Add new generator into your Gii module config:

```php
$config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    'generators' => [
        'crud' => [
            'class' => 'ma3obblu\gii\generators\crud\Generator',
        ],
        'entity' => [
            'class' => 'ma3obblu\gii\generators\entity\Generator',
        ],
        'form' => [
            'class' => 'ma3obblu\gii\generators\form\Generator',
        ],
        'handler' => [
            'class' => 'ma3obblu\gii\generators\handler\Generator',
        ],
        'fixture_class' => [
            'class' => 'ma3obblu\gii\generators\fixture_class\Generator',
        ],
        'fixture_data' => [
            'class' => 'ma3obblu\gii\generators\fixture_data\Generator',
        ]
    ],
];
```

And add the following line:

```php
Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
```

in top of your `config/web.php` file.
