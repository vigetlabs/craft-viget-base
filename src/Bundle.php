<?php

namespace viget\base;

use craft\web\AssetBundle;

class Bundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@viget/base/resources';

        $this->css = [
            'style.css',
        ];

        parent::init();
    }
}
