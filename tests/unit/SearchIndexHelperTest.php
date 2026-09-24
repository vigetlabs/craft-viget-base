<?php

namespace vigetbasetests\unit;

use Codeception\Test\Unit;
use Craft;
use viget\base\helpers\SearchIndex;
use yii\base\NotSupportedException;

class SearchIndexHelperTest extends Unit
{
    public function testMysqlOnlyOptimizesInnodbWhenFulltextOnlyIsOn(): void
    {
        $db = Craft::$app->getDb();
        if (!$db->getIsMysql()) {
            $this->markTestSkipped('MySQL only');
        }

        $engine = $db->createCommand(
            'SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table',
            [':table' => SearchIndex::tableName()],
        )->queryScalar();
        $fulltextOnly = (bool)$db->createCommand('SELECT @@GLOBAL.innodb_optimize_fulltext_only')->queryScalar();

        $result = SearchIndex::optimize();

        if ($engine === 'InnoDB' && !$fulltextOnly) {
            $this->assertFalse($result->ran);
            $this->assertStringContainsString('innodb_optimize_fulltext_only', $result->message);
        } else {
            $this->assertTrue($result->ran);
            $this->assertStringContainsString('OPTIMIZE TABLE', $result->message);
        }
    }

    public function testMysqlFullRebuildAlwaysRuns(): void
    {
        if (!Craft::$app->getDb()->getIsMysql()) {
            $this->markTestSkipped('MySQL only');
        }

        $result = SearchIndex::optimize(full: true);

        $this->assertTrue($result->ran);
        $this->assertStringContainsString(SearchIndex::tableName(), $result->message);
    }

    public function testPostgresVacuums(): void
    {
        if (!Craft::$app->getDb()->getIsPgsql()) {
            $this->markTestSkipped('PostgreSQL only');
        }

        $result = SearchIndex::optimize();

        $this->assertTrue($result->ran);
        $this->assertStringContainsString('VACUUM ANALYZE', $result->message);
    }

    public function testUnsupportedDriverThrows(): void
    {
        $db = Craft::$app->getDb();
        $driverName = $db->driverName;
        $db->driverName = 'unsupported_driver';

        try {
            $this->expectException(NotSupportedException::class);
            SearchIndex::optimize();
        } finally {
            $db->driverName = $driverName;
        }
    }

    public function testTableNameIsTheRealName(): void
    {
        $this->assertStringEndsWith('searchindex', SearchIndex::tableName());
        $this->assertStringNotContainsString('{{', SearchIndex::tableName());
    }
}
