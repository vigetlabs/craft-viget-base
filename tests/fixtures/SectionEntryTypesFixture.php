<?php

namespace vigetbasetests\fixtures;

use Craft;
use craft\db\Table;
use craft\services\Entries;
use yii\test\ActiveFixture;

/**
 * Assigns entry types to sections. Craft 5 keeps that in a join table with no record class.
 */
class SectionEntryTypesFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $tableName = Table::SECTIONS_ENTRYTYPES;

    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/section-entry-types.php';

    /**
     * @inheritdoc
     */
    public $depends = [SectionsFixture::class, EntryTypesFixture::class];

    /**
     * @inheritdoc
     */
    public function load(): void
    {
        parent::load();

        // The entries service memoizes sections and entry types, so swap in one that reads the fixtures.
        Craft::$app->set('entries', new Entries());
    }
}
