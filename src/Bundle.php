<?php

namespace viget\base;

use Craft;
use craft\web\AssetBundle;
use viget\base\services\PartsKit;

class Bundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@viget/base/resources';

        $css = [
            'css/style.css'
        ];

        // Is this a parts kit request
        if (PartsKit::isRequest()) {
            $css[] = 'css/parts-kit.css';

            $this->js = [
                'js/parts-kit.js',
            ];
        }

        $this->css = $css;

        parent::init();
    }
}
