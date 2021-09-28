<?php

namespace viget\base\controllers;

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
        $redirectUrl = Module::$instance->partsKit->getFirstNavUrl();

        if (!$redirectUrl) {
            throw new \Exception('Looks like you don’t have any parts kit components setup yet.');
        }

        return $this->redirect($redirectUrl, 301);
    }
}
