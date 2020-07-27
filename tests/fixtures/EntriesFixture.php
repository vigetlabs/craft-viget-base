<?php

namespace viget\base\tests\fixtures;

use craft\test\fixtures\elements\EntryFixture;
use viget\base\tests\fixtures\SectionsFixture;
use viget\base\tests\fixtures\EntryTypesFixture;

class EntriesFixture extends EntryFixture
{
    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/entries.php';

    /**
     * @inheritdoc
     */
    public $depends = [SectionsFixture::class, EntryTypesFixture::class];
}
