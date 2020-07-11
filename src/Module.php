<?php

namespace viget\base;

use Craft;
use yii\base\Event;
use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;
use craft\web\View;

use viget\base\twigextensions\Extension;
use viget\base\services\CpNav;
use viget\base\Bundle;

/**
 * Yii Module for setting up custom Twig functionality to keep templates streamlined
 */
class Module extends \yii\base\Module
{
    public static $instance;

    /**
     * Initializes the module.
     *
     * @return void
     */
    public function init()
    {
        Craft::setAlias('@modules/viget', $this->getBasePath());
        $this->controllerNamespace = 'modules\viget\controllers';

        parent::init();
        self::$instance = $this;

        $this->_setComponents();

        if (Craft::$app->request->getIsSiteRequest()) {
            $this->_bindEvents();

            Craft::$app->view->registerTwigExtension(new Extension());
        }

        if (Craft::$app->request->getIsCpRequest()) {
            $this->_bindCpEvents();
        }

        // Always turn on the debug bar in dev environment
        if (
            getenv('ENVIRONMENT') === 'dev' &&
            !Craft::$app->getRequest()->getIsConsoleRequest() &&
            !Craft::$app->getRequest()->getIsAjax()
        ) {
            Craft::$app->session->set('enableDebugToolbarForSite', true);
        }

        Craft::info(
            'viget module loaded',
            __METHOD__
        );
    }

    /**
     * Set components (services) on the module
     *
     * @return void
     */
    private function _setComponents()
    {
        $this->setComponents([
            'cpNav' => CpNav::class,
        ]);
    }

    /**
     * Bind actions onto Craft front-end events
     *
     * @return void
     */
    private function _bindEvents()
    {
        // Add edit entry link to front-end templates
        Event::on(
            View::class,
            View::EVENT_END_BODY,
            function(Event $e) {
                $element = Craft::$app->getUrlManager()->getMatchedElement();
                
                if (!$element) return;

                $currentUser = Craft::$app->getUser()->identity ?? null;

                if (
                    Craft::$app->config->general->devMode ||
                    ($currentUser && $currentUser->can('accessCp'))
                ) {
                    $this->view->registerAssetBundle(Bundle::class);

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
    }

    /**
     * Bind actions onto Craft CP events
     *
     * @return void
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
}
