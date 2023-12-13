<?php

namespace viget\base\controllers;

use yii\web\Response;

use viget\base\Module;

class PartsKitController extends \craft\web\Controller
{
    protected array|int|bool $allowAnonymous = true;

    /**
     * Redirect to the first component in the parts kit
     *
     * @return Response
     */
    public function actionRedirectIndex(): Response
    {
        $redirectUrl = Module::getInstance()->partsKit->getFirstNavUrl();

        if (!$redirectUrl) {
            throw new \Exception('Looks like you don’t have any parts kit components setup yet.');
        }

        return $this->redirect($redirectUrl, 301);
    }

    public function actionConfig(): Response
    {
        return $this->asJson([
            'schemaVersion' => '0.0.1',
            'nav' => Module::getInstance()->partsKit->getNav(),
        ]);
    }
}
