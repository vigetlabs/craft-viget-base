<?php

namespace vigetbasetests\fixtures;

use craft\records\EntryType;
use craft\test\Fixture;

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
