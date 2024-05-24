<?php

namespace unit\helpers;

use Codeception\Test\Unit;
use Craft;
use craft\db\Table;
use viget\base\helpers\SearchIndex;

class SearchIndexHelperTest extends Unit
{
    protected function setUp(): void
    {
        parent::setUp();
        Craft::$app->getDb()->createCommand()->truncateTable(Table::SEARCHINDEX)->execute();
    }

    public function testOptimizeForMysql()
    {
        if (Craft::$app->getDb()->driverName !== 'mysql') {
            $this->markTestSkipped('Skip MySQL testing when driver is not mysql');
        }

        $result = SearchIndex::optimize();

        // Assert: Verify that the result is an integer (number of rows affected)
        $this->assertIsInt($result);
    }

    public function testOptimizeForPostgres()
    {
        if (Craft::$app->getDb()->driverName !== 'pgsql') {
            $this->markTestSkipped('Skip PostgreSQL testing when driver is not pgsql');
        }

        $result = SearchIndex::optimize();

        // Assert: Verify that the result is an integer (number of rows affected)
        $this->assertIsInt($result);
    }

    public function testUnsupportedDriverThrowsException()
    {
        Craft::$app->getDb()->driverName = 'unsupported_driver';

        // Assert: Expect an exception to be thrown
        $this->expectException(\Exception::class);

        SearchIndex::optimize();
    }
}
