<?php

namespace viget\base\services;

use Craft;
use craft\helpers\Html;
use craft\helpers\ArrayHelper;

/**
 * Helper class for formatting GTM data attributes
 */
class Tracking
{
    /**
     * Return attributes for tracking imploded with a pipe
     *
     * @param Array|string ...$params
     * @return string
     */
    public static function getGtmAttribute(...$params): string
    {
        $attrs = [];

        foreach ($params as $param) {
            if (is_array($param)) {
                ArrayHelper::append($attrs, ...$param);
            } else {
                ArrayHelper::append($attrs, $param);
            }
        }

        return Html::encode(implode('|', array_filter($attrs)));
    }
}
