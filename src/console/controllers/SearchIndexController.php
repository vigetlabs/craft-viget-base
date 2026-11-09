<?php

namespace viget\base\console\controllers;

use craft\console\Controller;
use craft\helpers\Queue;
use Throwable;
use viget\base\helpers\SearchIndex;
use viget\base\jobs\OptimizeSearchIndex;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Maintains Craft's searchindex table.
 */
class SearchIndexController extends Controller
{
    /**
     * @var bool Rebuild the table even when its storage engine would not benefit. On InnoDB that
     * copies the whole table, blocks search index writes while it runs, and repeats the copy on
     * read replicas. On PostgreSQL it runs VACUUM FULL, which locks the table for reads too.
     */
    public bool $full = false;

    /**
     * @var bool Push the work to the queue, so a worker runs it and the Queue Manager tracks it.
     * Needs a dedicated queue worker, not Craft's web-request queue runner.
     */
    public bool $queue = false;

    /**
     * @inheritdoc
     */
    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), ['full', 'queue']);
    }

    /**
     * Optimizes Craft's searchindex table when its storage engine benefits from it.
     *
     * MyISAM and PostgreSQL tables are always maintained. An InnoDB table is only touched when the
     * server has innodb_optimize_fulltext_only enabled, unless --full asks for a rebuild.
     */
    public function actionOptimize(): int
    {
        if ($this->queue) {
            $id = Queue::push(new OptimizeSearchIndex(['full' => $this->full]));

            if ($id === null) {
                $this->stderr("The queue did not accept the search index job.\n", Console::FG_RED);

                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->success(sprintf('Queued job %s to %s the search index table.', $id, $this->full ? 'rebuild' : 'optimize'));

            return ExitCode::OK;
        }

        try {
            $result = SearchIndex::optimize($this->full);
        } catch (Throwable $e) {
            $this->stderr("Failed to optimize the search index table: {$e->getMessage()}\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (!$result->ran) {
            $this->note($result->message);
            $this->tip('Pass `--full` to rebuild the table anyway.');

            return ExitCode::OK;
        }

        $this->success($result->message);

        return ExitCode::OK;
    }
}
