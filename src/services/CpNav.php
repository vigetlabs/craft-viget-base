<?php

namespace viget\base\services;

use Craft;
use craft\elements\Entry;

use viget\base\Module;

/**
 * Modifies the CP nav
 */
class CpNav
{
    /**
     * Adds helpful items to the CP nav for development
     *
     * @param array $navItems
     * @return void
     */
    public function addItems(array $navItems = [])
    {
        $settings = Module::$config['cpNav'];

        // Add the "default" nav items
        if ($settings['useDefaults']) {
            $navItems[] = [
                'url' => 'settings/sections',
                'label' => Craft::t('app', 'Sections'),
                'fontIcon' => $settings['icon'],
            ];

            $navItems[] = [
                'url' => 'settings/fields',
                'label' => Craft::t('app', 'Fields'),
                'fontIcon' => $settings['icon'],
            ];
        }

        // Add any user defined nav items
        if ($settings['navItems']) {
            foreach ($settings['navItems'] as $navItem) {
                $navItems[] = [
                    'url' => $navItem['url'],
                    'label' => $navItem['label'],
                    'fontIcon' => $settings['icon'],
                ];
            }
        }

        // Add recent entries for quick access
        if ($settings['showRecentEntries'] > 0) {
            $entries = Entry::findAll([
                'orderBy' => 'dateUpdated desc',
                'limit' => $settings['showRecentEntries'],
            ]);

            foreach ($entries as $entry) {
                $navItems[] = [
                    'url' => $entry->cpEditUrl,
                    'label' => $entry->title,
                    'fontIcon' => 'time',
                ];
            }
        }

        return $navItems;
    }
}
