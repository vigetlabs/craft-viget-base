<?php

namespace viget\base;

use Craft;
use yii\base\Event;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\Cp;
use craft\web\View;
use craft\web\twig\variables\CraftVariable;
use craft\helpers\ArrayHelper;
use craft\services\Plugins;

use viget\base\Bundle;
use viget\base\twigextensions\Extension;
use viget\base\services\CpNav;
use viget\base\services\Util;
use viget\base\services\PhoneHome;
use viget\base\services\PartsKit;
use viget\base\services\Tailwind;
use viget\base\services\Tracking;

/**
 * Yii Module for setting up custom Twig functionality to keep templates streamlined
 */
class Module extends \yii\base\Module
{
    public static $instance;
    public static $config;

    /**
     * Initializes the module.
     */
    public function init()
    {
        Craft::setAlias('@viget/base', __DIR__);
        $this->controllerNamespace = 'viget\base\controllers';

        parent::init();
        self::$instance = $this;

        $this->_loadConfig();
        $this->_setComponents();

        if (Craft::$app->request->getIsSiteRequest()) {
            $this->_bindEvents();

            Craft::$app->view->registerTwigExtension(new Extension());

            if (!Craft::$app->request->getIsAjax() && !Craft::$app->request->getIsConsoleRequest()) {
                $this->view->registerAssetBundle(Bundle::class);
            }
        }

        if (Craft::$app->request->getIsCpRequest()) {
            $this->_bindCpEvents();

            // Phone home for Airtable inventory
            if (!Craft::$app->request->getIsAjax() && Craft::$app->isInstalled) {
                PhoneHome::makeCall();
            }
        }

        // Always turn on the debug bar and field handles in dev environment (for logged in users)
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_LOAD_PLUGINS,
            function (Event $event) {
                if (
                    Craft::$app->env === 'dev' &&
                    Craft::$app->user->checkPermission('accessCp') &&
                    !Craft::$app->request->getIsConsoleRequest()
                ) {
                    Craft::$app->user->identity->mergePreferences([
                        'enableDebugToolbarForSite' => true,
                        'enableDebugToolbarForCp' => true,
                        'showFieldHandles' => true,
                    ]);
                }
            }
        );

        Craft::info(
            'viget base loaded',
            __METHOD__
        );
    }

    /**
     * Set components (services) on the module
     */
    private function _setComponents()
    {
        $this->setComponents([
            'cpNav' => CpNav::class,
            'util' => Util::class,
            'partsKit' => PartsKit::class,
            'tailwind' => Tailwind::class,
            'tracking' => Tracking::class,
        ]);
    }

    /**
     * Bind actions onto Craft front-end events
     */
    private function _bindEvents()
    {
        // Bind to craft.viget sub-object
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $e) {
                $variable = $e->sender;
                $variable->set('viget', self::$instance);
            }
        );

        // Add edit entry link to front-end templates
        Event::on(
            View::class,
            View::EVENT_END_BODY,
            function (Event $e) {
                $element = Craft::$app->getUrlManager()->getMatchedElement();

                if (!$element) return;

                if (
                    Craft::$app->config->general->devMode ||
                    Craft::$app->user->checkPermission('accessCp')
                ) {
                    echo '<a
                            href="' . $element->cpEditUrl . '"
                            class="edit-entry"
                            target="_blank"
                            rel="noopener"
                          >
                            Edit Entry
                          </a>';
                }
            }
        );

        // Define viget base templates directory and index url
        if (self::$instance->partsKit->isRequest()) {
            Event::on(
                View::class,
                View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
                function (RegisterTemplateRootsEvent $e) {
                    if (is_dir($baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'templates')) {
                        $e->roots['viget-base'] = $baseDir;
                    }
                }
            );

            Event::on(
                UrlManager::class,
                UrlManager::EVENT_REGISTER_SITE_URL_RULES,
                function (RegisterUrlRulesEvent $event) {
                    $partsKitDir = self::$config['partsKit']['directory'];

                    $event->rules[$partsKitDir] = ['template' => 'viget-base/parts-kit/index'];
                }
            );
        }
    }

    /**
     * Bind actions onto Craft CP events
     */
    private function _bindCpEvents()
    {
        // If it's not devMode, don't modify nav
        if (!Craft::$app->config->general->devMode) {
            return;
        }

        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function (RegisterCpNavItemsEvent $event) {
                $event->navItems = $this->cpNav->addItems($event->navItems);
            }
        );
    }

    /**
     * Load module config
     *
     * @return void
     */
    private function _loadConfig()
    {
        $defaults = [
            'cpNav' => [
                'useDefaults' => true,
                'navItems' => [],
                'showRecentEntries' => 3,
                'icon' => 'disabled',
            ],
            'partsKit' => [
                'directory' => 'parts-kit',
                'layout' => '_layouts/app',
                'volume' => 'partsKit',
                'theme' => 'light',
            ],
            'tailwind' => [
                'configPath' => Craft::getAlias('@config/tailwind/tailwind.json'),
            ],
        ];

        $userSettings = Craft::$app->config->getConfigFromFile('viget');
        self::$config = ArrayHelper::merge($defaults, $userSettings);
    }
}
