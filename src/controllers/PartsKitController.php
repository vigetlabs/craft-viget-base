<?php

namespace viget\base\controllers;

use craft\helpers\UrlHelper;
use viget\base\models\PartsKitNavItem;
use viget\base\services\PartsKit;
use yii\web\Response;

use viget\base\Module;

class PartsKitController extends \craft\web\Controller
{
    protected $allowAnonymous = true;

    /**
     * Redirect to the first component in the parts kit
     *
     * @return Response
     */
    public function actionRedirectIndex(): Response
    {
        if (PartsKit::isV2()) {
           return $this->renderTemplate('viget-base/_parts-kit/v2-index');
        }

        $redirectUrl = Module::$instance->partsKit->getFirstNavUrl();

        if (!$redirectUrl) {
            throw new \Exception('Looks like you don’t have any parts kit components setup yet.');
        }

        return $this->redirect($redirectUrl, 301);
    }

    /**
     * Generates the config file for the parts kit
     * @return Response
     */
    public function actionConfig(): Response
    {
        /** @var PartsKit $partsKitService */
        $partsKitService = Module::getInstance()->partsKit;

        $nav = [];

        foreach ($partsKitService->getNav() as $key => $item) {

            $children = array_map(
                function(array $child) {
                    return new PartsKitNavItem([
                        'title' => $child['title'],
                        'url' => UrlHelper::url($child['url'], [
                            'version' => 2,
                        ]),
                        // Our current folder parsing doesn't support more than 2 levels.
                        // Eventually we'd like it to
                        'children' => [],
                    ]);
                },
                $item['items'] ?? []
            );

            $nav[] = new PartsKitNavItem([
                'title' => $key,
                'url' => '',
                'children' => $children,
            ]);
        }

        return $this->asJson([
            'schemaVersion' => '0.0.1',
            'nav' => $nav,
        ]);
    }
}
