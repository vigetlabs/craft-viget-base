<?php

namespace viget\base\helpers;

use Craft;
use craft\db\Connection;
use craft\db\Table;
use viget\base\models\OptimizeResult;
use yii\base\NotSupportedException;
use yii\db\Exception;

/**
 * Maintenance for Craft's searchindex table.
 */
class SearchIndex
{
    /**
     * @deprecated in 6.1.0. Use [[tableName()]] for the real table name, or `craft\db\Table::SEARCHINDEX`.
     */
    public static $tableName = Table::SEARCHINDEX;

    /**
     * Optimizes the searchindex table when its storage engine gets something out of it.
     *
     * - MySQL, MyISAM or Aria: `OPTIMIZE TABLE` defragments the data file after Craft's
     *   delete-and-reinsert churn.
     * - MySQL, InnoDB: Craft gives the table a FULLTEXT index, which rules out an in-place rebuild,
     *   so `OPTIMIZE TABLE` copies the whole table. Search index writes block for the duration, and
     *   a read replica repeats the copy when the statement replicates. InnoDB's clustered index does
     *   not fragment the way MyISAM's data file did, so the copy buys nothing. The exception is a
     *   server with `innodb_optimize_fulltext_only` enabled, where `OPTIMIZE TABLE` skips the copy
     *   and purges deleted documents from the FULLTEXT index, `innodb_ft_num_word_optimize` words
     *   per run. That statement is kept out of the binary log, because a replica applies it with
     *   its own setting and would run the copy if the setting is off there. Without the setting
     *   nothing runs, unless $full asks for the rebuild.
     * - PostgreSQL: `VACUUM ANALYZE`, or `VACUUM (FULL, ANALYZE)` when $full is set.
     *
     * @param bool $full Rebuild the table even when the engine would not benefit
     * @throws NotSupportedException for any other database driver or storage engine
     * @throws Exception when MySQL reports an error for the maintenance statement
     */
    public static function optimize(bool $full = false): OptimizeResult
    {
        $db = Craft::$app->getDb();

        if ($db->getIsPgsql()) {
            $sql = $full ? 'VACUUM (FULL, ANALYZE)' : 'VACUUM ANALYZE';
            $db->createCommand("$sql " . Table::SEARCHINDEX)->execute();

            return new OptimizeResult(true, sprintf('Ran %s on %s.', $sql, self::tableName()));
        }

        if (!$db->getIsMysql()) {
            throw new NotSupportedException("Unsupported database driver: {$db->driverName}");
        }

        // Yii sends every query*() call to a replica when one is configured, and the statements
        // below return result sets, so pin all of it to the primary: its engine and settings are
        // the ones that matter, and the rebuild must not run on a replica.
        return $db->usePrimary(function(Connection $db) use ($full): OptimizeResult {
            $table = self::tableName();
            $engine = self::engine($db);

            if ($engine === 'MyISAM' || $engine === 'Aria') {
                $status = self::run($db, 'OPTIMIZE TABLE ' . Table::SEARCHINDEX);

                return new OptimizeResult(true, "Ran OPTIMIZE TABLE on $table ($engine): $status.");
            }

            if ($engine !== 'InnoDB') {
                throw new NotSupportedException("Unsupported storage engine for $table: '$engine'");
            }

            if ($full) {
                // What OPTIMIZE TABLE does to an InnoDB table, a rebuild and then ANALYZE, spelled
                // out so innodb_optimize_fulltext_only cannot turn the requested rebuild into an
                // index purge.
                $db->createCommand('ALTER TABLE ' . Table::SEARCHINDEX . ' FORCE')->execute();
                $status = self::run($db, 'ANALYZE TABLE ' . Table::SEARCHINDEX);

                return new OptimizeResult(true, "Rebuilt $table (InnoDB) with ALTER TABLE FORCE and analyzed it: $status.");
            }

            if (!self::fulltextOnly($db)) {
                return new OptimizeResult(false, "Skipped $table: it is InnoDB, so OPTIMIZE TABLE would copy the whole table because of its FULLTEXT index, block search index writes meanwhile, and replay the copy on any read replica. Enable innodb_optimize_fulltext_only on the server to purge deleted documents from the index instead (note that it limits OPTIMIZE TABLE to FULLTEXT indexes for every InnoDB table on the server), or ask for a full rebuild.");
            }

            $status = self::run($db, 'OPTIMIZE NO_WRITE_TO_BINLOG TABLE ' . Table::SEARCHINDEX);

            return new OptimizeResult(true, "Ran OPTIMIZE TABLE on $table (InnoDB with innodb_optimize_fulltext_only on, so only the FULLTEXT index was processed, and not on replicas): $status.");
        });
    }

    /**
     * The table's real name, prefix included, rather than Craft's `{{%searchindex}}` placeholder.
     */
    public static function tableName(): string
    {
        return Craft::$app->getDb()->getSchema()->getRawTableName(Table::SEARCHINDEX);
    }

    private static function engine(Connection $db): string
    {
        return (string)$db->createCommand(
            'SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table',
            [':table' => self::tableName()],
        )->queryScalar();
    }

    private static function fulltextOnly(Connection $db): bool
    {
        return (bool)$db->createCommand('SELECT @@GLOBAL.innodb_optimize_fulltext_only')->queryScalar();
    }

    /**
     * Runs a table maintenance statement and returns the status MySQL reports for it, such as
     * "OK" or "Table is already up to date".
     *
     * @throws Exception when a result row has the error message type, which is how these
     * statements report most failures instead of raising a SQL error
     */
    private static function run(Connection $db, string $sql): string
    {
        $rows = $db->createCommand($sql)->queryAll();
        $errors = array_filter($rows, fn(array $row) => strtolower($row['Msg_type']) === 'error');

        if ($errors) {
            throw new Exception("$sql failed: " . implode('; ', array_column($errors, 'Msg_text')));
        }

        return implode('; ', array_column($rows, 'Msg_text'));
    }
}
