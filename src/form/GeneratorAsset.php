<?php
namespace ma3oBblu\gii\generators\form;

use yii\web\AssetBundle;

/**
 * Class GeneratorAsset
 * @package ma3oBblu\gii\form
 */
class GeneratorAsset extends AssetBundle
{
    public $sourcePath = '@ma3obblu/gii/form/assets';
    public $js = [
        'generator.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}