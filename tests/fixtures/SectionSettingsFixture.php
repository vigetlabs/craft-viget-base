<?php

namespace vigetbasetests\fixtures;

use craft\records\Section_SiteSettings;
use craft\test\Fixture;

class SectionSettingsFixture extends Fixture
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
