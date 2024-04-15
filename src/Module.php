<?php

namespace viget\base;

use Craft;
use craft\base\Element;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\BootstrapInterface;
use yii\base\Event;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\twig\variables\Cp;
use craft\web\View;
use craft\web\twig\variables\CraftVariable;
use craft\helpers\ArrayHelper;
use craft\web\Application as CraftWebApplication;
use viget\base\controllers\PartsKitController;
use viget\base\twigextensions\Extension;
use viget\base\services\CpNav;
use viget\base\services\Util;
use viget\base\services\PhoneHome;
use viget\base\services\PartsKit;
use viget\base\services\Tailwind;
use viget\base\services\Tracking;

/**
 * Yii Module for setting up custom Twig functionality to keep templates streamlined
 * @property-read PartsKit $partsKit
 * @property-read CpNav $cpNav
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    public static $config;

    public function bootstrap($app)
    {
        Craft::setAlias('@viget/base', __DIR__);


        Craft::$app->onInit(function() {
            self::setInstance($this);
            $this->_loadConfig();
            $this->_setComponents();

            // Auto-bootstrapping requires that we
            // manually register our controller paths
            if (Craft::$app instanceof CraftWebApplication) {
                Craft::$app->controllerMap['parts-kit'] = [
                    'class' => PartsKitController::class,
                ];
            }

            if (Craft::$app->request->getIsSiteRequest()) {
                $this->_bindEvents();

                Craft::$app->view->registerTwigExtension(new Extension());
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
                CraftWebApplication::class,
                CraftWebApplication::EVENT_INIT,
                static function (Event $event) {
                    if (
                        Craft::$app->env === 'dev' &&
                        self::$config['yiiDebugBar'] === true &&
                        Craft::$app->user->checkPermission('accessCp') &&
                        !Craft::$app->request->getIsConsoleRequest()
                    ) {
                        /**
                         * Enables Yii debug bar when in dev mode
                         * @see craft\web\Application::debugBootstrap()
                         */
                        $request = Craft::$app->getRequest();
                        $request->headers->add('X-Debug', 'enable');
                    }
                }
            );

            Craft::info(
                'viget base loaded',
                __METHOD__
            );
        });
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
                $variable->set('viget', self::getInstance());
            }
        );

        // Add edit entry link to front-end templates
        Event::on(
            View::class,
            View::EVENT_END_BODY,
            static function (Event $e) {
                if (
                    self::$config['disableEditButton']
                ) {
                    return;
                }

                if (
                    Craft::$app->config->general->devMode ||
                    Craft::$app->user->checkPermission('accessCp')
                ) {

                    /** @var UrlManager $urlManager */
                    $urlManager = Craft::$app->getUrlManager();
                    $element = $urlManager->getMatchedElement();

                    if ($element instanceof Element === false) {
                        return;
                    }

                    echo '<a
                            href="' . $element->cpEditUrl . '"
                            style="
                                background: #e5422b;
                                border-top-right-radius: 5px;
                                bottom: 0;
                                color: #fff;
                                font-size: 14px;
                                left: 0;
                                line-height: 1;
                                padding: 10px;
                                position: fixed;
                                text-decoration: none;
                                z-index: 10;
                            "
                            target="_blank"
                            rel="noopener"
                          >
                            Edit Entry
                          </a>';
                }
            }
        );

        // Define viget base templates directory and index url
        if (self::getInstance()->partsKit->isRequest()) {
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

        // Override rendering of the root /parts-kit URL, so we can render a custom template that
        // injects the HTML / JS for our parts kit UI.
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $partsKitDir = self::$config['partsKit']['directory'];
                $event->rules[$partsKitDir] = 'parts-kit/root';
            }
        );
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
            'yiiDebugBar' => true,
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
            ],
            'tailwind' => [
                'configPath' => Craft::getAlias('@config/tailwind/tailwind.json'),
            ],
            'disableEditButton' => false,
        ];

        $userSettings = Craft::$app->config->getConfigFromFile('viget');
        self::$config = ArrayHelper::merge($defaults, $userSettings);
    }
}
