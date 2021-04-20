<?php

namespace viget\base;

use Craft;
use yii\base\Event;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\twig\variables\Cp;
use craft\web\View;
use craft\web\twig\variables\CraftVariable;
use craft\helpers\ArrayHelper;

use viget\base\Bundle;
use viget\base\twigextensions\Extension;
use viget\base\services\CpNav;
use viget\base\services\Util;
use viget\base\services\PhoneHome;
use viget\base\services\PartsKit;
use viget\base\services\Tailwind;

/**
 * Yii Module for setting up custom Twig functionality to keep templates streamlined
 */
class Module extends \yii\base\Module
{
    public static $instance;
    public static $config;
    private static $_currentUser;

    /**
     * Initializes the module.
     */
    public function init()
    {
        Craft::setAlias('@viget/base', __DIR__);
        $this->controllerNamespace = 'viget\base\controllers';

        parent::init();
        self::$instance = $this;
        self::$_currentUser = Craft::$app->user->identity ?? null;

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
        if (
            getenv('ENVIRONMENT') === 'dev' &&
            self::$_currentUser &&
            !Craft::$app->request->getIsConsoleRequest()
        ) {
            self::$_currentUser->mergePreferences([
                'enableDebugToolbarForSite' => true,
                'enableDebugToolbarForCp' => true,
                'showFieldHandles' => true,
            ]);
        }

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
                    (self::$_currentUser && self::$_currentUser->can('accessCp'))
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

        // Define viget base templates directory
        if (PartsKit::isRequest()) {
            Event::on(
                View::class,
                View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
                function (RegisterTemplateRootsEvent $e) {
                    if (is_dir($baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'templates')) {
                        $e->roots['viget-base'] = $baseDir;
                    }
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
