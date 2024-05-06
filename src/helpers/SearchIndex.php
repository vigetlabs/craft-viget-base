<?php

namespace viget\base\helpers;

use Craft;
use craft\db\Table;

class SearchIndex
{
    public static $tableName = Table::SEARCHINDEX;

    /**
     * Optimizes Craft's searchindex table (can take a long time)
     * @return int number of rows affected by the execution.
     * @throws Exception execution failed | unsupported driver
     * @see \yii\db\Command::execute()
     */
    public static function optimize()
    {
        $db = Craft::$app->getDb();
        $driverName = $db->driverName;
        $tableName = self::$tableName;

        if ($driverName === 'mysql') {
            return $db->createCommand("OPTIMIZE TABLE {$tableName}")->execute();
        } elseif ($driverName === 'pgsql') {
            return $db->createCommand("VACUUM ANALYZE {$tableName}")->execute();
        } else {
            throw new \Exception('Unsupported database driver');
        }
    }
}
