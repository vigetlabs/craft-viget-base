<?php

namespace viget\base\twigextensions;

use Craft;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use viget\base\services\PartialLoader;

/**
 * Custom Twig Extensions
 */
class Extension extends AbstractExtension
{
    /**
     * Register Twig functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'partial',
                [PartialLoader::class, 'load'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
