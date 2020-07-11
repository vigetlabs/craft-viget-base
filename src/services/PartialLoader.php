<?php

namespace viget\base\services;

use Craft;

/**
 * Shortcut for including a file with only the variables passed and ignore missing
 * Same as {% include 'path/file' ignore missing with { key: 'value' } only %}
 */
class PartialLoader
{
    /**
     * Load a template
     *
     * @param string $template
     * @param array $variables
     * @return string
     */
    public static function load(string $template, array $variables = []): string
    {
        // If the template can't be found, log it, and return nothing
        if (!Craft::$app->view->doesTemplateExist($template)) {
            Craft::error(
                "Error locating template: {$template}",
                __METHOD__
            );

            return '';
        }

        return Craft::$app->view->renderTemplate($template, $variables);
    }
}
