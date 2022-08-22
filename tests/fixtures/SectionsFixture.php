<?php

namespace vigetbasetests\fixtures;

use Craft;
use craft\records\Section;
use craft\services\Sections;
use craft\test\ActiveFixture;


class SectionsFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/sections.php';

    /**
     * @inheritdoc
     */
    public $modelClass = Section::class;

    /**
     * @inheritdoc
     */
    public $depends = [SectionSettingsFixture::class];

    /**
     * @inheritdoc
     */
    public function load():void
    {
        parent::load();

        Craft::$app->set('sections', new Sections());
    }
}
