<?php

namespace vigetbasetests\unit;

use Codeception\Test\Unit;

use UnitTester;
use Craft;
use craft\elements\Entry;

use viget\base\Module;
use vigetbasetests\fixtures\EntriesFixture;

class FillInEntriesTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public $entryIds = [];
    public $params = [
        'section' => 'testSection1',
    ];

    public function _fixtures(): array
    {
        return [
            'entries' => [
                'class' => EntriesFixture::class,
            ],
        ];
    }

    protected function _before()
    {
        $this->eventIds = Entry::find()->section('testSection1')->ids();
    }

    public function testNotEnough()
    {
        $selectedEntries = Entry::findAll([
            'section' => 'testSection1',
            'limit' => 2,
        ]);

        $entries = Module::$instance->util->fillInEntries($selectedEntries, $this->params, 4);

        $this->assertEquals(count($entries), 4);
    }

    public function testEnoughPassed()
    {
        $selectedEntries = Entry::findAll([
            'section' => 'testSection1',
            'limit' => 4,
        ]);

        $entries = Module::$instance->util->fillInEntries($selectedEntries, $this->params, 4);

        $this->assertEquals(count($entries), 4);
    }

    public function testNonePassed()
    {
        $entries = Module::$instance->util->fillInEntries([], $this->params, 4);

        $this->assertEquals(count($entries), 4);
    }

    public function testEntryIds()
    {
        $id = array_pop($this->eventIds);

        $selectedEntry = Entry::findAll([
            'section' => 'testSection1',
            'id' => $id,
            'limit' => 1,
        ]);

        $otherEvents = Entry::findAll([
            'section' => 'testSection1',
            'id' => 'not ' . $id,
            'limit' => 2,
        ]);

        $additionalIds = array_map(function($event) {
            return $event->id;
        }, $otherEvents);

        $expectedIds = array_merge([$id], $additionalIds);

        $entries = Module::$instance->util->fillInEntries($selectedEntry, $this->params, 3);

        $returnedIds = array_map(function($event) {
            return $event->id;
        }, $entries);

        $this->assertEquals($returnedIds, $expectedIds);
    }
}
