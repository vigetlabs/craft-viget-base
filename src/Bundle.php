<?php

namespace viget\base;

use Craft;
use craft\web\AssetBundle;

class Bundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@viget/base/resources';

        $css = [
            'css/style.css'
        ];

        // Is this a parts kit request
        if (($firstSegment = Craft::$app->request->segments[0] ?? null) === 'parts-kit') {
            $css[] = 'css/parts-kit.css';

            $this->js = [
                'js/parts-kit.js',
            ];
        }

        $this->css = $css;

        parent::init();
    }
}
