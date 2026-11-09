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

        // Craft creates the table as InnoDB on every MySQL version the test environments run.
        $engine = $db->createCommand(
            'SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table',
            [':table' => SearchIndex::tableName()],
        )->queryScalar();
        $this->assertSame('InnoDB', $engine);

        $fulltextOnly = (bool)$db->createCommand('SELECT @@GLOBAL.innodb_optimize_fulltext_only')->queryScalar();

        $result = SearchIndex::optimize();

        if ($fulltextOnly) {
            $this->assertTrue($result->ran);
            $this->assertStringContainsString('innodb_optimize_fulltext_only on', $result->message);
        } else {
            $this->assertFalse($result->ran);
            $this->assertStringContainsString('Enable innodb_optimize_fulltext_only', $result->message);
        }
    }

    public function testMysqlFullRebuildAlwaysRuns(): void
    {
        if (!Craft::$app->getDb()->getIsMysql()) {
            $this->markTestSkipped('MySQL only');
        }

        $result = SearchIndex::optimize(full: true);

        $this->assertTrue($result->ran);
        $this->assertStringContainsString('ALTER TABLE FORCE', $result->message);
        $this->assertStringContainsString(SearchIndex::tableName(), $result->message);
    }

    public function testPostgresVacuums(): void
    {
        $this->endTestTransactionForVacuum();

        $result = SearchIndex::optimize();

        $this->assertTrue($result->ran);
        $this->assertStringContainsString('VACUUM ANALYZE', $result->message);
    }

    public function testPostgresFullRebuildRunsVacuumFull(): void
    {
        $this->endTestTransactionForVacuum();

        $result = SearchIndex::optimize(full: true);

        $this->assertTrue($result->ran);
        $this->assertStringContainsString('VACUUM (FULL, ANALYZE)', $result->message);
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

    /**
     * Skips the test off PostgreSQL. On it, commits the transaction Codeception wraps each test in,
     * since PostgreSQL refuses to run VACUUM inside a transaction block. The test writes nothing,
     * so there is nothing for the rollback to undo.
     */
    private function endTestTransactionForVacuum(): void
    {
        $db = Craft::$app->getDb();
        if (!$db->getIsPgsql()) {
            $this->markTestSkipped('PostgreSQL only');
        }

        $db->getTransaction()?->commit();
    }
}
