<?php

namespace writealt\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class WriteAltAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../../resources';
    public $js = ['js/writealt.js'];
    public $css = ['css/writealt.css'];
    public $depends = [CpAsset::class];
}
