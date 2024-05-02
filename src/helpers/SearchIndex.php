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
     * @throws Exception execution failed
     * @see \yii\db\Command::execute()
     */
    public static function optimize()
    {
        $db = Craft::$app->getDb();
        $tableName = self::$tableName;

        return $db->createCommand("OPTIMIZE TABLE {$tableName}")->execute();
    }
}
