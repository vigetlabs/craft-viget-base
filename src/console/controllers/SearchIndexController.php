<?php

namespace viget\base\console\controllers;

use craft\console\Controller;
use craft\helpers\Queue;
use Throwable;
use viget\base\helpers\SearchIndex;
use viget\base\jobs\OptimizeSearchIndex;
use yii\console\ExitCode;

/**
 * Maintains Craft's searchindex table.
 */
class SearchIndexController extends Controller
{
    /**
     * @var bool Rebuild the table even when its storage engine would not benefit. On InnoDB that
     * copies the whole table and blocks search index writes while it runs.
     */
    public bool $full = false;

    /**
     * @var bool Push the work to the queue, so a worker runs it and the Queue Manager tracks it.
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
            $this->success(sprintf('Queued job %s to %s the search index table.', $id, $this->full ? 'rebuild' : 'optimize'));

            return ExitCode::OK;
        }

        try {
            $result = SearchIndex::optimize($this->full);
        } catch (Throwable $e) {
            $this->failure("Failed to optimize the search index table: {$e->getMessage()}");

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
