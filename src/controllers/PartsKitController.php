<?php

namespace viget\base\controllers;

use yii\web\Response;

use viget\base\Module;

class PartsKitController extends \craft\web\Controller
{
    protected array|int|bool $allowAnonymous = true;

    public function actionConfig(): Response
    {
        return $this->asJson([
            'schemaVersion' => '0.0.1',
            'nav' => Module::getInstance()->partsKit->getNav(),
        ]);
    }

    public function actionRoot(): Response
    {
        return $this->renderTemplate(
            'viget-base/parts-kit-root',
        );
    }
}
