<?php

namespace viget\base\services;

use Craft;
use craft\helpers\Json;
use yii\base\Component;

use viget\base\Module;

/**
 * Functionality for accessing Tailwind tokens
 */
class Tailwind extends Component
{
    public $tailwindConfig = null;

    public function init()
    {
        parent::init();
        $this->_loadFullConfig();
    }

    /**
     * Get Tailwind config object
     */
    public function getFullConfig(): ?array
    {
        return $this->tailwindConfig;
    }

    /**
     * Get classname-ready list of colors
     */
    public function getColors(): array
    {
        if (!$this->tailwindConfig) return [];

        $colors = $this->tailwindConfig['theme']['colors'];
        $names = [];

        foreach ($colors as $name => $value) {
            if (is_array($value)) {
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

    /**
     * Load Tailwind config object from JSON
     */
    private function _loadFullConfig()
    {
        $tailwindPath = Module::$config['tailwind']['configPath'];

        try {
            $config = file_get_contents($tailwindPath);
            $this->tailwindConfig = Json::decodeIfJson($config);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
