<?php

namespace vigetbasetests\fixtures;

use craft\records\Section;
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
}
