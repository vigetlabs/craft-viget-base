<?php

namespace viget\base\services;

use Craft;
use yii\base\Component;

/**
 * Functionality for accessing Tailwind tokens
 */
class Tailwind extends Component
{
    public $tailwindConfig = null;

    /**
     * Get a config item either the default or from the config file
     *
     * @param string $key
     * @return string|array|null
     */
    public static function getConfig(string $key)
    {
        // Merge user settings with the defaults
        $userSettings = Craft::$app->config->getConfigFromFile('viget')['tailwind'] ?? [];
        $config = array_merge(
            [
                'configPath' => CRAFT_BASE_PATH . '/config/tailwind/tailwind.json'
            ],
            $userSettings
        );

        return $config[$key] ?? null;
    }

    /**
     * Get Tailwind config object
     */
    public function getTwConfig(): ?object
    {
        if ($this->tailwindConfig) return $this->tailwindConfig;

        $tailwindPath = self::getConfig('configPath');

        try {
            $config = file_get_contents($tailwindPath);
            $this->tailwindConfig = json_decode($config);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            return $this->tailwindConfig;
        }
    }

    /**
     * Get classname-ready list of colors
     */
    public function getColors(): array
    {
        $config = $this->getTwConfig();
        if (!$config) return [];

        $colors = $config->theme->colors;
        $names = [];

        foreach ($colors as $name => $value) {
            if (is_object($value)) {
                foreach ($value as $shade => $hex) {
                    if ($shade === 'default') {
                        $names[$name] = $hex;
                        continue;
                    }
                    $names[$name . '-' . $shade] = $hex;
                }
            } else {
                $names[$name] = $value;
            }
        }
        return $names;
    }
}
