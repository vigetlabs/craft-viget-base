<?php

namespace vigetbasetests\fixtures;

use craft\records\Section_SiteSettings;
use craft\test\ActiveFixture;

class SectionSettingsFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/section-settings.php';

    /**
     * @inheritdoc
     */
    public $modelClass = Section_SiteSettings::class;
}
