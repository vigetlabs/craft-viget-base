<?php

namespace viget\base\tests\fixtures;

use craft\records\EntryType;
use craft\test\Fixture;
use viget\base\tests\fixtures\SectionsFixture;

class EntryTypesFixture extends Fixture
{
    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/entry-types.php';

    /**
     * @inheritdoc
     */
    public $modelClass = EntryType::class;

    /**
     * @inheritdoc
     */
    public $depends = [SectionsFixture::class];
}
