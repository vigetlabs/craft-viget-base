<?php

namespace vigetbasetests\fixtures;

use Craft;
use craft\db\Table;
use craft\helpers\Db;
use craft\records\Section;
use craft\services\Entries;
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
    public $depends = [SectionSettingsFixture::class, EntryTypesFixture::class];

    /**
     * @var array Entry type IDs from each data row's `entryTypes` key, keyed like the data file
     */
    private array $entryTypeIds = [];

    /**
     * @inheritdoc
     */
    public function load(): void
    {
        parent::load();

        // Craft 5 assigns entry types to sections in a join table that has no record class.
        foreach ($this->entryTypeIds as $key => $typeIds) {
            $rows = [];
            foreach (array_values($typeIds) as $i => $typeId) {
                $rows[] = [$this->ids[$key], $typeId, $i + 1];
            }
            Db::batchInsert(Table::SECTIONS_ENTRYTYPES, ['sectionId', 'typeId', 'sortOrder'], $rows);
        }

        // The entries service memoizes sections and entry types, so swap in one that reads the fixtures.
        Craft::$app->set('entries', new Entries());
    }

    /**
     * @inheritdoc
     */
    public function unload(): void
    {
        // Foreign key checks are off while fixtures unload, so deleting a section won't cascade to
        // its entry type links. Delete them first, while $this->ids still holds this fixture's sections.
        Db::delete(Table::SECTIONS_ENTRYTYPES, ['sectionId' => array_values($this->ids)]);

        parent::unload();
    }

    /**
     * @inheritdoc
     */
    protected function loadData($file, $throwException = true)
    {
        $data = parent::loadData($file, $throwException);
        $this->entryTypeIds = [];

        // `entryTypes` isn't a sections column, so pull it out before the record is saved.
        foreach ($data as $key => $row) {
            if (array_key_exists('entryTypes', $row)) {
                $this->entryTypeIds[$key] = $row['entryTypes'];
                unset($data[$key]['entryTypes']);
            }
        }

        return $data;
    }
}
