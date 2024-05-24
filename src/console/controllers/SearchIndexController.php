<?php

namespace viget\base\console\controllers;

use craft\console\Controller;
use viget\base\helpers\SearchIndex;
use yii\console\ExitCode;

class SearchIndexController extends Controller
{
    /**
     * Optimizes Craft's searchindex table (can take a long time)
     * @return int
     */
    public function actionOptimize(): int
    {
        $tableName = SearchIndex::$tableName;

        try {
            SearchIndex::optimize();
            $this->stdout("Table {$tableName} optimized successfully.\n");
            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("Failed to optimize table {$tableName}: {$e->getMessage()}\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
